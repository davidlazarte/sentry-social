<?php

return array(

	/**
	 * Callback URL
	 */
	'callback_url' => 'sentrysocial/auth/callback',

	/**
	 * Social Providers
	 */
	'providers' => array(

		/**
		 * Examples
		 *
		 *	 'twitter' => array(
		 *		'app_id'     => 'gHvPp5IlA4fKxzTnS8a0w',
		 *		'app_secret' => 'KajMPNHhiAoI5sn1Wvh33NLJF0L0bnX9N3l89ZWTXU',
		 *		'driver'     => 'OAuth',
		 *	),
		 *
		 * 'facebook' => array(
		 *		'app_id'     => 'your app id',
		 *		'app_secret' => 'your app secret',
		 *      'scope'      => array('offline_access'),
		 *		'driver'     => 'OAuth2' // or OAuth - what protocal it uses
		 *	),
		 */

		'twitter' => array(
			'app_id'     => 'gHvPp5IlA4fKxzTnS8a0w',
			'app_secret' => 'KajMPNHhiAoI5sn1Wvh33NLJF0L0bnX9N3l89ZWTXU',
			'driver'     => 'OAuth',
		),

		'facebook' => array(
			'app_id'     => '507183095963402',
			'app_secret' => '466168e23984ae645a898790c2f342d3',
			'scope'      => array('email', 'offline_access'),
			'driver'     => 'OAuth2'
		),

		'google' => array(
			'app_id'     => '496043082049',
			'app_secret' => '7SBROVhOec1_d1Gqk4UYZH_l',
			'driver'     => 'OAuth2'
		)

	)
);