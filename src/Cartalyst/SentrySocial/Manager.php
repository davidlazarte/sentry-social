<?php namespace Cartalyst\SentrySocial;
/**
 * Part of the Sentry Social package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\SentrySocial\SocialLinks\Eloquent\Provider as SocialLinkProvider;
use Cartalyst\SentrySocial\SocialLinks\ProviderInterface as SocialLinkProviderInterface;
use Cartalyst\SentrySocial\Services\ServiceInterface;
use Cartalyst\SentrySocial\Services\ServiceFactory;
use Illuminate\Database\Eloquent\Model;
use OAuth\Common\Consumer\Credentials;

class Manager {

	/**
	 * The Sentry instance.
	 *
	 * @var Cartalyst\Sentry\Sentry
	 */
	protected $sentry;

	/**
	 * The social link provider, used for tying logins
	 * to Sentry logins.
	 *
	 * @var Cartalyst\SentrySocial\SocialLinks\ProviderInterface
	 */
	protected $socialLinkProvider;

	/**
	 * The Service Factory, used for creating
	 * service instances.
	 *
	 * @var Cartalyst\SentrySocial\ServiceFactory
	 */
	protected $serviceFactory;

	/**
	 * Array of registered connections.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Create a new Sentry Social manager.
	 *
	 * @param  Cartalyst\SentrySocial\SocialLinks\ProviderInterface  $socialLinkProvider
	 * @param  Cartalyst\Sentry\ServiceFactory  $serviceFactory
	 * @param  array  $connections
	 * @return void
	 */
	public function __construct(Sentry $sentry, SocialLinkProviderInterface $socialLinkProvider = null, ServiceFactory $serviceFactory = null, array $connections = array())
	{
		$this->sentry             = $sentry;
		$this->socialLinkProvider = $socialLinkProvider ?: new SocialLinkProvider;
		$this->serviceFactory     = $serviceFactory ?: new ServiceFactory;

		foreach ($connections as $name => $connection)
		{
			$this->register($name, $connection);
		}
	}

	/**
	 * Registers a connection with the manager.
	 *
	 * @param  string  $name
	 * @param  array   $connection
	 * @return void
	 */
	public function register($name, array $connection)
	{
		// Default the connection service to be the
		// same as the connection name if it is
		// not provided.
		if ( ! isset($connection['service'])) $connection['service'] = $name;

		$this->connections[$name] = $connection;
	}

	/**
	 * Register a custom OAuth2 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOAuth2Service($className)
	{
		$this->serviceFactory->registerOAuth2Service($className);
	}

	/**
	 * Register a custom OAuth1 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOAuth1Service($className)
	{
		$this->serviceFactory->registerOAuth1Service($className);
	}

	/**
	 * Makes a new service from a connection with
	 * the given name.
	 *
	 * @param  string  $name
	 * @param  string  $callback
	 * @return Cartalyst\SentrySocial\Services\ServiceInterface
	 * @todo   Add proper storage options (illuminate/database for example).
	 */
	public function make($name, $callback = null)
	{
		$connection  = $this->getConnection($name, $callback);

		$credentials = $this->createCredentials($connection['key'], $connection['secret'], $callback);

		$storage = $this->createStorage($connection['service']);

		$scopes = isset($connection['scopes']) ? $connection['scopes'] : array();

		return $this->serviceFactory->createService($connection['service'], $credentials, $storage, $scopes);
	}

	/**
	 * Authenticates the given Sentry Social OAuth service.
	 *
	 * @param  Cartalyst\SentrySocial\Services\ServiceInterface  $service
	 * @param  string  $code
	 * @param  bool    $remember
	 * @return Cartalyst\Sentry\Users\UserInterface  $user
	 * @todo   Add a "email_changed_from_social" field to `users` and update
	 *         email address if different when authenticating??
	 */
	public function authenticate(ServiceInterface $service, $code, $remember = false)
	{
		$this->sentry->logout();

		$service->requestAccessToken($code);

		$serviceName = $service->getServiceName();
		$uid         = $service->getUserUniqueIdentifier();

		$link = $this->socialLinkProvider->findLink($serviceName, $uid);

		// If we have no user associated with the link, we'll register one now
		if ( ! $user = $link->getUser())
		{
			$provider = $this->sentry->getUserProvider();
			$login    = $service->getUserEmail() ?: "{$uid}@{$serviceName}";

			// Lazily create a user
			try
			{
				$user = $provider->findByLogin($login);
			}
			catch (UserNotFoundException $e)
			{
				$emptyUser = $provider->getEmptyUser();

				// Create a dummy password for the user
				$passwordParams = array($serviceName, $uid, $login, time(), mt_rand());
				shuffle($passwordParams);

				// Setup an array of attributes we'll add onto
				// so we can create our user.
				$attributes = array(
					$emptyUser->getLoginName()    => $login,
					$emptyUser->getPasswordName() => implode('', $passwordParams),
				);

				// Some providers give a first / last name, some don't.
				// If we only have one name, we'll just put it in the
				// "first_name" attribute.
				if (is_array($name = $service->getUserName()))
				{
					$attributes['first_name'] = $name[0];
					$attributes['last_name']  = $name[1];
				}
				elseif (is_string($name))
				{
					$attributes['first_name'] = $name;
				}

				$user = $provider->create($attributes);
				$user->attemptActivation($user->getActivationCode());
			}

			$link->setUser($user);
		}

		$throttleProvider = $this->sentry->getThrottleProvider();

		// Now, we'll check throttling to ensure we're
		// not logging in a user which shouldn't be allowed.
		if ($throttleProvider->isEnabled())
		{
			$throttle = $throttleProvider->findByUserId(
				$user->getId(),
				$this->sentry->getIpAddress()
			);

			$throttle->check();
		}

		$this->sentry->login($user, $remember);

		return $user;
	}

	/**
	 * Gets a connection registered with the manager
	 * with the given name. Callbacks can be overridden
	 * at runtime.
	 *
	 * @param  string|array  $name
	 * @param  string  $callback
	 * @return array
	 */
	protected function getConnection($name, $callback = null)
	{
		// If our connection is already an array,
		// the developer is creating a connection
		// on the fly, without registering it.
		if (is_array($name))
		{
			$connection = $name;
		}

		// Otherwise, we will retrieve it from the array
		// of registered connections.
		else
		{
			if ( ! isset($this->connections[$name]))
			{
				throw new \RuntimeException("Cannot make connection [$name] as it has not been registered.");
			}

			$connection = $this->connections[$name];
		}

		// Validate the connection
		if ( ! isset($connection['key']) or ! isset($connection['secret']) or ! isset($connection['service']))
		{
			throw new \RuntimeException("Invalid connection configuration passed.");
		}

		// If a runtime callback has been passed, override
		// the connection with it.
		if (isset($callback))
		{
			$connection['callback'] = $callback;
		}

		if ( ! isset($callback))
		{
			$message = 'No callback for connection.';
			if (is_string($name))
			{
				$message = "No callback for [$name] connection.";
			}

			throw new \RuntimeException($message);
		}

		return $connection;
	}

	/**
	 * Creates a Credentials object from the given
	 * application key, secret and callback URL.
	 *
	 * @param  string  $key
	 * @param  string  $secret
	 * @param  string  $callback
	 * @return void
	 */
	protected function createCredentials($key, $secret, $callback)
	{
		return new Credentials($key, $secret, $callback);
	}

	protected function createStorage($service)
	{
		return new \OAuth\Common\Storage\Session(true, 'oauth_token_'.$service);
	}

}
