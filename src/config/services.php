<?php
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

return array(

	/*
	|--------------------------------------------------------------------------
	| Service Connections
	|--------------------------------------------------------------------------
	|
	| Here, you may specify any number of connection configurations your
	| application requires.
	|
	| Each connection must specify the following:
	| 1. "service" - a valid authentication type (optional). Valid types are:
	|    (OAuth 1): "fitbit", "twitter"
	|    (OAuth 2): "bitly", "facebook", "foursquare", "github", "google",
	|               "microsoft", "soundcloud", "yammer".
	|    If the "service" key is not specified, we will use the array key for
	|    the configuration to guess the type. This allows for convenience
	|    as well as multiple configurations with the same "service".
	|    We plan on adding support for more providers in the future.
	| 2. "key" - your application's key.
	| 3. "secret" - your application's secret.
	|
	| OAuth2 providers can also provide the following:
	| 1. "scopes" - an array of scopes you are requesting access to (optional).
	|
	| All connections are optional, feel free to replace with
	| your own at will.
	|
	*/
	'connections' => array(

		'bitly' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'facebook' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array('email'),
		),

		'fitbit' => array(
			'key'    => '',
			'secret' => '',
		),

		'foursquare' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'github' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array('user'),
		),

		'google' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array('userinfo_profile', 'userinfo_email'),
		),

		'microsoft' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array('emails'),
		),

		'soundcloud' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'twitter' => array(
			'key'    => '',
			'secret' => '',
		),

		'yammer' => array(
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		/*
		// Example of using a different key
		// to the "service". This allows for multiple
		// configurations for the one
		'main' => array(
			'service' => 'google',
			'key'     => '',
			'secret'  => '',
			'scopes'  => array('userinfo_email', 'userinfo_profile'),
		),
		*/

	),

);
