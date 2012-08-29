<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * @package    Sentry Social
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    http://getplatform.com/manuals/sentrysocial/license
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	/**
	 * URL's
	 */
	'url' => array(
		// request callback url
		'callback'      => 'sentrysocial/auth/callback',

		// register
		'register'      => 'sentrysocial/auth/register',

		// login page
		'login'         => 'login',

		// authenticated
		'authenticated' => '',
	),


	/**
	 * Social Providers
	 */
	'providers' => array(

		/**
		 * Examples
		 *
		 *	 'twitter' => array(
		 *		'app_id'     => 'your app id',
		 *		'app_secret' => 'your app secrete',
		 *		'driver'     => 'OAuth',
		 *	),
		 *
		 * 'facebook' => array(
		 *		'app_id'     => 'your app id',
		 *		'app_secret' => 'your app secret',
		 *      'scope'      => array('offline_access'),
		 *		'driver'     => 'OAuth2'
		 *	),
		 */

	)
);