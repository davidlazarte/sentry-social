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

use Cartalyst\SentrySocial\Links\Eloquent\Provider as LinkProvider;
use Cartalyst\SentrySocial\RequestProviders\IlluminateProvider as RequestProvider;
use Cartalyst\Sentry\Sessions\IlluminateSession;

class SentrySocialServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('cartalyst/sentry-social', 'cartalyst/sentry-social');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerLinkProvider();

		$this->registerRequestProvider();

		$this->registerSession();

		$this->registerSentrySocial();
	}

	protected function registerLinkProvider()
	{
		$this->app['sentry.social.link'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry-social::link'];

			return new LinkProvider($model);
		});
	}

	protected function registerRequestProvider()
	{
		$this->app['sentry.social.request'] = $this->app->share(function($app)
		{
			return new RequestProvider($app['request']);
		});
	}

	protected function registerSession()
	{
		$this->app['sentry.social.session'] = $this->app->share(function($app)
		{
			$key = $app['config']['cartalyst/sentry::cookie.key'].'_social';

			return new IlluminateSession($app['session'], $key);
		});
	}

	/**
	 * Registers Sentry Social.
	 *
	 * @return void
	 */
	protected function registerSentrySocial()
	{
		$this->app['sentry.social'] = $this->app->share(function($app)
		{
			$manager = new Manager(
				$app['sentry'],
				$app['sentry.social.link'],
				$app['sentry.social.request'],
				$app['sentry.social.session'],
				$app['events']
			);

			$connections = $app['config']['cartalyst/sentry-social::connections'];

			$manager->addConnections($connections);
		});
	}

}
