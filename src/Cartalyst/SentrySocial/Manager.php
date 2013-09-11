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

use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Sentry;

class Manager {

	protected $sentry;

	protected $session;

	protected $linkProvider;

	protected $connections = array();

	public function __construct(Sentry $sentry, SessionInterface $session = null, LinkProviderInterface $linkProvider = null)
	{

	}

	public function make($slug, $callbackUri = null)
	{
		$connection = $this->getConnection($slug);

		return $this->createProvider($connection, $callbackUri);
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

	protected function determineOAuthType($driver)
	{
		// Built-in OAuth1 server
		if (class_exists('League\\OAuth1\\Client\\Server\\'.$driver))
		{
			return 1;
		}

		// Built-in OAuth2 provider
		if (class_exists('League\\OAuth2\\Client\\Provider\\'.$driver))
		{
			return 2;
		}

		// If the driver is a custom class which doesn't exist
		if ( ! class_exists($driver))
		{
			throw new \RuntimeException("Failed to determine OAuth class [$driver] does not exist.");
		}

		$parent = $this->getHighestParent($driver);

		if ($parent == 'League\\OAuth1\\Client\\Server\\Server')
		{
			return 1;
		}

		if ($parent == 'League\\OAuth2\\Client\\Provider\\IdentityProvider')
		{
			return 2;
		}

		throw new \RuntimeException("Driver [$driver] does not inherit from a compatible OAuth provider class.");
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

}
