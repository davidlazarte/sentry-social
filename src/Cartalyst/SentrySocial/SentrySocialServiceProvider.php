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

class SentrySocialServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerServiceFactory();

		$this->registerSentrySocial();
	}

	protected function registerServiceFactory()
	{
		$this->app['sentry.social.factory'] = $this->app->share(function($app)
		{
			return new ServiceFactory;
		});
	}

	protected function registerSentrySocial()
	{
		$this->app['sentry.social'] = $this->app->share(function($app)
		{
			return new Manager($app['sentry.social.factory']);
		});
	}

}
