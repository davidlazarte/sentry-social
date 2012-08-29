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
	// 'callback_url' => 'sentrysocial/auth/callback',

	'url' => array(
		// request callback url
		'callback'      => 'sentrysocial/auth/callback',

		// user canceled authorization
		'cancel'        => 'login',

		// register
		'register'      => 'sentrysocial/auth/register',

		// authenticated
		'authenticated' => '',

		// login
		'login'         => 'login',
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