<?php namespace Cartalyst\SentrySocial\Tests;
/**
 * Part of the Data Grid package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Data Grid
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\SentrySocial\Manager;
use PHPUnit_Framework_TestCase;

class ManagerTest extends PHPUnit_Framework_TestCase {

	protected $manager;

	protected $sentry;

	protected $requestProvider;

	protected $session;

	protected $dispatcher;

	protected $linkProvider;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass()
	{
		// require_once __DIR__.'/stubs/ci/CI_Session.php';
	}

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->manager = new Manager(
			$this->sentry          = m::mock('Cartalyst\Sentry\Sentry'),
			$this->linkProvider    = m::mock('Cartalyst\SentrySocial\Links\ProviderInterface'),
			$this->requestProvider = m::mock('Cartalyst\SentrySocial\RequestProviders\ProviderInterface'),
			$this->session         = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'),
			$this->dispatcher      = m::mock('Illuminate\Events\Dispatcher')
		);
	}

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testAddingConnection()
	{
		$this->manager->addConnection('foo', array('bar' => 'baz'));
		$this->assertCount(1, $this->manager->getConnections());
		$this->assertEquals(array('bar' => 'baz'), $this->manager->getConnection('foo'));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testGettingNonExistentConnection()
	{
		$this->manager->getConnection('foo');
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testMakeNonExistentConnection()
	{
		$this->manager->make('foo');
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Class matching driver is required
	 */
	public function testMakeConnectionWithMissingDriver()
	{
		$this->manager->addConnection('foo', array());
		$this->manager->make('foo');
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage App identifier and secret are required
	 */
	public function testMakeConnectionWithMissingIdentifier()
	{
		$this->manager->addConnection('foo', array(
			'driver' => 'Foo',
		));
		$this->manager->make('foo');
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage App identifier and secret are required
	 */
	public function testMakeConnectionWithMissingSecret()
	{
		$this->manager->addConnection('foo', array(
			'driver' => 'Foo',
			'identifier' => 'bar',
		));
		$this->manager->make('foo');
	}

	public function testMakeBuiltInConnection()
	{
		$this->manager->addConnection('facebook', array(
			'driver'     => 'Facebook',
			'identifier' => 'appid',
			'secret'     => 'appsecret',
		));

		var_dump($this->manager->make('facebook'));
	}

}
