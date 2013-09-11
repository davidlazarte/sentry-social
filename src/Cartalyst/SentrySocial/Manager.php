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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\SentrySocial\Links\Eloquent\Provider as LinkProvider;
use Cartalyst\SentrySocial\Links\ProviderInterface as LinkProviderInterface;
use Cartalyst\SentrySocial\RequestProviders\NativeProvider as NativeRequestProvider;
use Cartalyst\SentrySocial\RequestProviders\ProviderInterface as RequestProviderInterface;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Sentry;
use Closure;

class Manager {

	protected $sentry;

	protected $requestProvider;

	protected $session;

	protected $linkProvider;

	/**
	 * The event dispatcher instance.
	 *
	 * @var Illuminate\Events\Dispacher
	 */
	protected $dispatcher;

	protected $connections = array();

	protected $providers = array();

	public function __construct(Sentry $sentry, RequestProviderInterface $requestProvider = null, SessionInterface $session = null, Dispacher $dispatcher = null, LinkProviderInterface $linkProvider = null)
	{
		$this->sentry = $sentry;
		$this->requestProvider = $requestProvider ?: new NativeRequestProvider;
		$this->session = $session ?: new NativeSession('cartalyst_sentry_social');

		if (isset($dispatcher))
		{
			$this->dispatcher = $dispatcher;
		}

		$this->linkProvider = $linkProvider ?: new LinkProvider;
	}

	public function make($slug, $callbackUri = null)
	{
		if ( ! isset($this->providers[$slug]))
		{
			$connection = $this->getConnection($slug);

			$this->providers[$slug] = $this->createProvider($connection, $callbackUri);
		}

		return $this->providers[$slug];
	}

	public function getAuthorizeUri($slug, $callbackUri = null)
	{
		$provider = $this->make($slug, $callbackUri);

		// OAuth 1 is a three-legged authentication process
		// and thus we need to grab temporary credentials
		// first.
		if ($this->determineOAuthType($provider) == 1)
		{
			$temporaryCredentials = $provider->getTemporaryCredentials();

			$this->session->put($temporaryCredentials);

			return $provider->getAuthorizeUri($temporaryCredentials);
		}

		return $provider->getAuthorizeUri();
	}

	public function authenticate($slug, Closure $callback = null, $remember = false)
	{
		// If a callback was supplied, we'll treat it as
		// a global authenticating callback. Specific
		// callbacks for registering and existing
		// users can be registered outside of
		// this method.
		if ($callback) $this->authenticating($callback);

		$provider = $this->make($slug, $callbackUri);
		$token    = $this->retrieveToken($provider);

		$link = $this->link($slug, $provider);
		$user = $link->getUser();

		$this->login($user, $remember);

		return $user;
	}

	protected function link($slug, $provider, $token)
	{
		$link = $this->linkProvider->findLink($slug, $provider);
		$link->storeToken($token);

		if ( ! $user = $link->getUser())
		{
			$userProvider = $this->sentry->getUserProvider();
			$login        = $provider->getUserEmail() ?: $provider->getUserUid();

			try
			{
				$user = $userProvider->findByLogin($login);
				$link->setUser($user);

				$this->fireEvent('existing', $link, $provider, $token, $slug);
			}
			catch (UserNotFoundException $e)
			{
				$emptyUser = $userProvider->getEmptyUser();

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

				$user = $userProvider->create($attributes);
				$user->attemptActivation($user->getActivationCode());

				$link->setUser($user);

				$this->fireEvent('registering', $link, $provider, $token, $slug);
			}
		}

		$this->fireEvent('authenticating', $link, $provider, $token, $slug);

		return $link;
	}

	protected function login(UserInterface $user, $remember)
	{
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
	}

	protected function retrieveToken($provider)
	{
		if ($this->determineOAuthType($provider) == 1)
		{
			$temporaryIdentifier = $this->requestProvider->getOAuth1TemporaryIdentifier();
			$verifier = $this->requestProvider->getOAuth1Verifier();

			if ( ! $temporaryIdentifier)
			{
				throw new \AccessMissingException('Missing [oauth_token] parameter (used for OAuth1 temporary credentials identifier).');
			}

			if ( ! $verifier)
			{
				throw new \AccessMissingException('Missing [verifier] parameter.');
			}

			$temporaryCredentials = $this->session->get();

			$tokenCredentials = $provider->getTokenCredentials($temporaryCredentials, $temporaryIdentifier, $verifier);

			return $tokenCredentials;
		}

		$code = $this->requestProvider->getOAuth2Code();

		if ( ! $code)
		{
			throw new \AccessMissingException("Missing [code] parameter.");
		}

		$accessToken = $provider->getAccessToken('authorization_code', compact('code'));

		return $accessToken;
	}

	public function addConnection($slug, array $connection)
	{
		$this->connections[$slug] = $connection;
	}

	public function addConnections(array $connections)
	{
		foreach ($connections as $slug => $connection)
		{
			$this->addConnection($connection);
		}
	}

	public function getConnection($slug)
	{
		if ( ! isset($this->connections[$slug]))
		{
			throw new \RuntimeException("Cannot retrieve connection [$slug] as it has not been added.");
		}

		return $this->connections[$slug];
	}

	/**
	 * Set the event dispatcher.
	 *
	 * @param  Illuminate\Events\Dispatcher
	 * @return void
	 */
	public function setDispatcher(Dispacher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	protected function createProvider($connection, $callbackUri = null)
	{
		$this->validateConnection($connection);

		$oauthType = $this->determineOAuthType($connection['driver']);

		if ($oauthType == 1)
		{
			return $this->createOAuth1Provider($connection, $callbackUri);
		}

		return $this->createOAuth2Provider($connection, $callbackUri);
	}

	protected function validateConnection($connection)
	{
		if ( ! isset($connection['driver']))
		{
			throw new \InvalidArgumentException("Class matching driver is required for [$slug] connection.");
		}

		if ( ! isset($connection['identifier']) or ! isset($connection['secret']))
		{
			throw new \InvalidArgumentException("App identifier and secret are required for [$slug] connection.");
		}
	}

	protected function determineOAuthType($provider)
	{
		// Determined based on class name
		if (is_string($provider))
		{
			// Built-in OAuth1 server
			if (class_exists('League\\OAuth1\\Client\\Server\\'.$provider))
			{
				return 1;
			}

			// Built-in OAuth2 provider
			if (class_exists('League\\OAuth2\\Client\\Provider\\'.$provider))
			{
				return 2;
			}

			// If the driver is a custom class which doesn't exist
			if ( ! class_exists($provider))
			{
				throw new \RuntimeException("Failed to determine OAuth type as [$provider] does not exist.");
			}

			$parent = $this->getHighestParent($provider);

			if ($parent == 'League\\OAuth1\\Client\\Server\\Server')
			{
				return 1;
			}

			if ($parent == 'League\\OAuth2\\Client\\Provider\\IdentityProvider')
			{
				return 2;
			}

			throw new \RuntimeException("[$provider] does not inherit from a compatible OAuth provider class.");
		}

		if ($provider instanceof League\OAuth1\Client\Server\Server)
		{
			return 1;
		}

		if ($provider instanceof League\OAuth2\Client\Provider\IdentityProvider)
		{
			return 2;
		}

		throw new \RuntimeException('['.get_class($provider).'] does not inherit from a compatible OAuth provider class.');
	}

	protected function getHighestParent($driver)
	{
		// Find out what interfaces the driver implements
		$childClass = new \ReflectionClass($driver);

		while ($parentClass = $childClass->getParentClass())
		{
			$parentName = $parentClass->getName();
			$childClass = $parentClass;
		}

		return $parentName;
	}

	protected function createOAuth1Provider($connection, $callbackUri = null)
	{
		$driver = $connection['driver'];

		$credentials = array(
			'identifier'   => $connection['identifier'],
			'secret'       => $connection['secret'],
			'callback_uri' => $callbackUri,
		);

		return new $driver($credentials);
	}

	protected function createOAuth2Provider($connection, $callbackUri = null)
	{
		$driver = $connection['driver'];

		$options = array(
			'clientId'     => $connection['identifier'],
			'clientSecret' => $connection['secret'],
			'redirectUri'  => $callbackUri,
			'scopes'       => isset($connection['scopes']) ? $connection['scopes'] : array(),
		);

		return new $driver($options);
	}

	/**
	 * Fires an event for Sentry Social.
	 *
	 * @param  string  $name
	 * @param  Cartalyst\Sentry\Users\UserInterface  $user
	 * @return mixed
	 */
	protected function fireEvent($name, LinkInterface $link, $provider, $token, $slug)
	{
		if ( ! isset($this->dispatcher)) return;

		return $this->dispatcher->fire("sentry.social.{$name}", array($link, $provider, $token, $slug));
	}

}
