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

use OAuth\Common\Consumer\Credentials;

class Manager {

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
	 * @param  Cartalyst\Sentry\ServiceFactory  $serviceFactory
	 * @param  array  $connections
	 * @return void
	 */
	public function __construct(ServiceFactory $serviceFactory = null, array $connections = array())
	{
		$this->serviceFactory = $serviceFactory ?: new ServiceFactory;

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
	 * Makes a new service from a connection with
	 * the given name.
	 *
	 * @param  string  $name
	 * @param  string  $callback
	 * @return OAuth\Common\Service\ServiceInterface
	 * @todo   Add proper storage options (illuminate/database for example).
	 */
	public function make($name, $callback = null)
	{
		$connection  = $this->getConnection($name, $callback);

		$credentials = $this->createCredentials($connection['key'], $connection['secret'], $callback);

		// @todo, make our own storage engines.
		$storage = new \OAuth\Common\Storage\Session(true, 'oauth_token_'.$connection['service']);

		$scopes = isset($connection['scopes']) ? $connection['scopes'] : array();

		return $this->serviceFactory->createService($connection['service'], $credentials, $storage, $scopes);
	}

	/**
	 * Register a custom OAuth2 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOauth2Service($className)
	{
		$this->serviceFactory->registerOauth2Service($className);
	}

	/**
	 * Register a custom OAuth1 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOauth1Service($className)
	{
		$this->serviceFactory->registerOauth1Service($className);
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
		if ( ! isset($connection['key']) or ! isset($connection['secret'] or ! isset($connection['service'])))
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

}
