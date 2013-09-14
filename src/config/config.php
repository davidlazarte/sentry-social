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

	'connections' => array(

		// 'bitly' => array(
		// 	'driver'     => 'bitly',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array(),
		// ),

		'facebook' => array(
			'driver'     => 'Facebook',
			'identifier' => '',
			'secret'     => '',
			'scopes'     => array('email'),
		),

		// 'fitbit' => array(
		// 	'driver'     => 'Fitbit',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// ),

		// 'foursquare' => array(
		// 	'driver'     => 'Foursquare',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array(),
		// ),

		// 'github' => array(
		// 	'driver'     => 'GitHub',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array('user'),
		// ),

		// 'google' => array(
		// 	'driver'     => 'Google',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array('userinfo_profile', 'userinfo_email'),
		// ),

		// 'microsoft' => array(
		// 	'driver'     => 'Microsoft',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array('emails'),
		// ),

		// 'soundcloud' => array(
		// 	'driver'     => 'SoundCloud',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array(),
		// ),

		'twitter' => array(
			'driver'     => 'Twitter',
			'identifier' => '',
			'secret'     => '',
		),

		// 'yammer' => array(
		// 	'driver'     => 'Yammer',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array(),
		// ),

		// 'vkontakte' => array(
		// 	'driver'     => 'Vkontakte',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array(),
		// ),

		// 'linkedin' => array(
		// 	'driver'     => 'LinkedIn',
		// 	'identifier' => '',
		// 	'secret'     => '',
		// 	'scopes'     => array('r_fullprofile', 'r_emailaddress'),
		// ),

	),

	/*
	|--------------------------------------------------------------------------
	| Social Link Model
	|--------------------------------------------------------------------------
	|
	| When users are registered, a "social link provider" will map the social
	| authentications with user instances. Feel free to use your own model
	| with our provider.
	|
	|
	*/

	'link' => 'Cartalyst\SentrySocial\SocialLinks\Eloquent\SocialLink',

);
