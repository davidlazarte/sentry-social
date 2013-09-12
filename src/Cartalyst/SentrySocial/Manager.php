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
use Cartalyst\SentrySocial\Links\LinkInterface as LinkInterface;
use Cartalyst\SentrySocial\Links\ProviderInterface as LinkProviderInterface;
use Cartalyst\SentrySocial\RequestProviders\NativeProvider as NativeRequestProvider;
use Cartalyst\SentrySocial\RequestProviders\ProviderInterface as RequestProviderInterface;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserInterface;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Closure;
use Illuminate\Events\Dispatcher;
use League\OAuth1\Client\Server\Server as OAuth1Provider;
use League\OAuth2\Client\Provider\IdentityProvider as OAuth2Provider;

class Manager {

	/**
	 * The shared Sentry instance.
	 *
	 * @var \Cartalyst\Sentry\Sentyr
	 */
	protected $sentry;

	/**
	 * The link provider, used for associating users
	 * with OAuth providers.
	 *
	 * @var \Cartalyst\SentrySocial\Links\ProviderInterface
	 */
	protected $linkProvider;

	/**
	 * The request provider.
	 *
	 * @var \Cartalyst\SentrySocial\RequestProviders\ProviderInterface
	 */
	protected $requestProvider;

	/**
	 * A Sentry session driver.
	 *
	 * @var \Cartalyst\Sentry\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * The event dispatcher instance.
	 *
	 * @var Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Array of connectiosn (credentials for
	 * creating provider instances).
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Array of initialized providers.
	 *
	 * @var array
	 */
	protected $providers = array();

	/**
	 * Create a new Sentry Social Manager instance.
	 *
	 * @param  \Cartalyst\Sentry\Sentry  $sentry
	 * @param  \Cartalyst\SentrySocial\Links\ProviderInterface  $linkProvider
	 * @param  \Cartalyst\SentrySocial\RequestProviders\ProviderInterface  $requestProvider
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct(Sentry $sentry, LinkProviderInterface $linkProvider = null, RequestProviderInterface $requestProvider = null, SessionInterface $session = null, Dispatcher $dispatcher = null)
	{
		$this->sentry          = $sentry;
		$this->linkProvider    = $linkProvider ?: new LinkProvider;
		$this->requestProvider = $requestProvider ?: new NativeRequestProvider;
		$this->session         = $session ?: new NativeSession('cartalyst_sentry_social');

		if (isset($dispatcher))
		{
			$this->dispatcher = $dispatcher;
		}
	}

	/**
	 * Make a provider with the given connection slug
	 * and an optional callback URI. Instances of
	 * providers are cached.
	 *
	 * @param  string  $slug
	 * @param  string  $callbackUri
	 * @return mixed
	 */
	public function make($slug, $callbackUri = null)
	{
		if ( ! isset($this->providers[$slug]))
		{
			$this->providers[$slug] = $this->createProvider($slug, $callbackUri);
		}

		return $this->providers[$slug];
	}

	/**
	 * Returns the authorize URI for a connection with the given
	 * slug. Abstracts away the differences between OAuth1 and
	 * OAuth2 for a uniform API.
	 *
	 * @param  string  $slug
	 * @param  string  $callbackUri
	 * @return string
	 */
	public function getAuthorizeUri($slug, $callbackUri = null)
	{
		$provider = $this->make($slug, $callbackUri);

		// OAuth 1 is a three-legged authentication process
		// and thus we need to grab temporary credentials
		// first.
		if ($this->oauthVersion($provider) == 1)
		{
			$temporaryCredentials = $provider->getTemporaryCredentials();

			$this->session->put($temporaryCredentials);

			return $provider->getAuthorizeUri($temporaryCredentials);
		}

		return $provider->getAuthorizeUri();
	}

	/**
	 * Authenticate against the current service. A closure may be provided
	 * for a callback upon authentication as a shortcut for subscribing
	 * to an event.
	 *
	 * @param  string  $slug
	 * @param  Closure $callback
	 * @param  bool    $remmber
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function authenticate($slug, Closure $callback = null, $remember = false)
	{
		// If a callback was supplied, we'll treat it as
		// a global linking callback. Specific callbacks
		// for registering and existing users can be
		// registered outside of this method.
		if ($callback) $this->linking($callback);

		$provider = $this->make($slug);
		$token    = $this->retrieveToken($provider);

		$link = $this->link($slug, $provider, $token);
		$user = $link->getUser();

		$this->login($user, $remember);

		return $user;
	}

	/**
	 * Register a callback for when a new user is registered
	 * through an OAuth provider.
	 *
	 * @param  \Closure $callback
	 * @return void
	 */
	public function registering(Closure $callback)
	{
		$this->registerEvent('registering', $callback);
	}

	/**
	 * Register a callback for when an existing user is
	 * linked with an OAuth provider.
	 *
	 * @param  \Closure $callback
	 * @return void
	 */
	public function existing(Closure $callback)
	{
		$this->registerEvent('existing', $callback);
	}

	/**
	 * Register a callback for when a user of any type is
	 * linked with an OAuth provider.
	 *
	 * @param  \Closure $callback
	 * @return void
	 */
	public function linking(Closure $callback)
	{
		$this->registerEvent('linking', $callback);
	}

	/**
	 * Retrieves a link and associates a user (will lazily
	 * create one) for the given slug, provider and token.
	 *
	 * @param  string  $slug
	 * @param  mixed   $provider
	 * @param  mixed   $token
	 * @return \Cartalyst\SentrySocial\Links\LinkInterface
	 */
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
				$passwordParams = array($slug, $login, time(), mt_rand());
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
				if (is_array($name = $provider->getUserScreenName()))
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
		else
		{
			$this->fireEvent('existing', $link, $provider, $token, $slug);
		}

		$this->fireEvent('linking', $link, $provider, $token, $slug);

		return $link;
	}

	/**
	 * Logs the given user into Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 */
	protected function login(UserInterface $user, $remember = false)
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

	/**
	 * Retrieves a token (OAuth1 token credentials or OAuth2 access
	 * token) for the given provider, abstracting away the
	 * differences from the user.
	 *
	 * @param  mixed  $provider
	 * @return mixed
	 */
	protected function retrieveToken($provider)
	{
		if ($this->oauthVersion($provider) == 1)
		{
			$temporaryIdentifier = $this->requestProvider->getOAuth1TemporaryIdentifier();

			if ( ! $temporaryIdentifier)
			{
				throw new AccessMissingException('Missing [oauth_token] parameter (used for OAuth1 temporary credentials identifier).');
			}

			$verifier = $this->requestProvider->getOAuth1Verifier();

			if ( ! $verifier)
			{
				throw new AccessMissingException('Missing [verifier] parameter.');
			}

			$temporaryCredentials = $this->session->get();

			$tokenCredentials = $provider->getTokenCredentials($temporaryCredentials, $temporaryIdentifier, $verifier);

			return $tokenCredentials;
		}

		$code = $this->requestProvider->getOAuth2Code();

		if ( ! $code)
		{
			throw new AccessMissingException("Missing [code] parameter.");
		}

		$accessToken = $provider->getAccessToken('authorization_code', compact('code'));

		return $accessToken;
	}

	/**
	 * Add a connection to the manager.
	 *
	 * @param  string  $slug
	 * @param  array   $connection
	 * @return void
	 */
	public function addConnection($slug, array $connection)
	{
		$this->connections[$slug] = $connection;
	}

	/**
	 * Add multple connections to the manager.
	 *
	 * @param  array  $connections
	 * @return void
	 */
	public function addConnections(array $connections)
	{
		foreach ($connections as $slug => $connection)
		{
			$this->addConnection($connection);
		}
	}

	/**
	 * Retrieve a connection with the given slug.
	 *
	 * @param  string  $slug
	 * @return array
	 * @throws \RuntimeException
	 */
	public function getConnection($slug)
	{
		if ( ! isset($this->connections[$slug]))
		{
			throw new \RuntimeException("Cannot retrieve connection [$slug] as it has not been added.");
		}

		return $this->connections[$slug];
	}

	/**
	 * Get all connections associated with the
	 * manager.
	 *
	 * @return array
	 */
	public function getConnections()
	{
		return $this->connections;
	}

	/**
	 * Set the event dispatcher.
	 *
	 * @param  \Illuminate\Events\Dispatcher
	 * @return void
	 */
	public function setDispatcher(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Creates a provider from a connection with the given slug.
	 *
	 * @param  string  $slug
	 * @param  string  $callbackUri
	 * @return mixed
	 */
	protected function createProvider($slug, $callbackUri = null)
	{
		$connection = $this->getConnection($slug);

		$this->validateConnection($slug, $connection);

		list($oauthVersion, $driver) = $this->determineOAuth($connection['driver']);

		if ($oauthVersion == 1)
		{
			return $this->createOAuth1Provider($driver, $connection, $callbackUri);
		}

		return $this->createOAuth2Provider($driver, $connection, $callbackUri);
	}

	/**
	 * Validates the given connection is satisfactory to initiate a driver.
	 *
	 * @param  string  $slug
	 * @param  array   $connection
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function validateConnection($slug, array $connection)
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

	/**
	 * Determines the OAuth version of the given provider.
	 *
	 * @param  mixed  $provider
	 * @return int
	 * @throws \RuntimeException
	 */
	protected function oauthVersion($provider)
	{
		if ($provider instanceof OAuth1Provider)
		{
			return 1;
		}

		if ($provider instanceof OAuth2Provider)
		{
			return 2;
		}

		throw new \RuntimeException('['.get_class($provider).'] does not inherit from a compatible OAuth provider class.');
	}

	/**
	 * Determines the OAuth version and class name for a driver
	 * with the given name. Allows for built-in and custom
	 * drivers. An array is returned, where the first
	 * value is the version and the second is the
	 * class name to instantiate.
	 *
	 * @param  mixed  $driver
	 * @return array
	 * @throws \RuntimeException
	 */
	protected function determineOAuth($driver)
	{
		// Built-in OAuth1 server
		if (class_exists($class = 'League\\OAuth1\\Client\\Server\\'.$driver))
		{
			return array(1, $class);
		}

		// Built-in OAuth2 provider
		if (class_exists($class = 'League\\OAuth2\\Client\\Provider\\'.$driver))
		{
			return array(2, $class);
		}

		// If the driver is a custom class which doesn't exist
		if ( ! class_exists($driver))
		{
			throw new \RuntimeException("Failed to determine OAuth type as [$driver] does not exist.");
		}

		$parent = $this->getHighestParent($driver);

		if ($parent == 'League\\OAuth1\\Client\\Server\\Server')
		{
			return array(1, $driver);
		}

		if ($parent == 'League\\OAuth2\\Client\\Provider\\IdentityProvider')
		{
			return array(2, $driver);
		}

		throw new \RuntimeException("[$driver] does not inherit from a compatible OAuth provider class.");
	}

	/**
	 * Retrieves the highest parent class name for
	 * the given child class name.
	 *
	 * @param  string  $childName
	 * @return string  $parentName
	 */
	protected function getHighestParent($childName)
	{
		// Find out what interfaces the driver implements
		$childClass = new \ReflectionClass($childName);

		// We'll reference the child name as the default
		// parent name just incase somebody passes through
		// an object which doesn't extend anything, this'll
		// help them get a decent error message.
		$parentName = $childName;

		while ($parentClass = $childClass->getParentClass())
		{
			$parentName = $parentClass->getName();
			$childClass = $parentClass;
		}

		return $parentName;
	}

	/**
	 * Creates an OAuth1 provider from a connection with an optional
	 * callback URI.
	 *
	 * @param  string $driver
	 * @param  array  $connection
	 * @param  string $callbackUri
	 * @return \League\OAuth1\Client\Server\Server
	 */
	protected function createOAuth1Provider($driver, $connection, $callbackUri = null)
	{
		$credentials = array(
			'identifier'   => $connection['identifier'],
			'secret'       => $connection['secret'],
			'callback_uri' => $callbackUri,
		);

		return new $driver($credentials);
	}

	/**
	 * Creates an OAuth2 provider from a connection with an optional
	 * callback URI.
	 *
	 * @param  string $driver
	 * @param  array  $connection
	 * @param  string $callbackUri
	 * @return \League\OAuth2\Client\Provider\IdentityProvider
	 */
	protected function createOAuth2Provider($driver, $connection, $callbackUri = null)
	{
		$options = array(
			'clientId'     => $connection['identifier'],
			'clientSecret' => $connection['secret'],
			'redirectUri'  => $callbackUri,
			'scopes'       => isset($connection['scopes']) ? $connection['scopes'] : array(),
		);

		return new $driver($options);
	}

	/**
	 * Register an event with Sentry Social.
	 *
	 * @param  string   $name
	 * @param  \Closure $callback
	 * @return void
	 */
	protected function registerEvent($name, Closure $callback)
	{
		if ( ! isset($this->dispatcher))
		{
			throw new \RuntimeException("Events dispatcher has not been set.");
		}

		$this->dispatcher->listen("sentry.social.{$name}", $callback);
	}

	/**
	 * Fires an event for Sentry Social.
	 *
	 * @param  string  $name
	 * @param  \Cartalyst\SentrySocial\Links\LinkInterface  $link
	 * @param  mixed   $provider
	 * @param  mixed   $token
	 * @param  string  $slug
	 * @return mixed
	 */
	protected function fireEvent($name, LinkInterface $link, $provider, $token, $slug)
	{
		if ( ! isset($this->dispatcher)) return;

		return $this->dispatcher->fire("sentry.social.{$name}", array($link, $provider, $token, $slug));
	}

}
