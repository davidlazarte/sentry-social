<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cartalyst
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2012, Cartalyst LLC
 * @link       http://http://sentry.cartalyst.com/licence.html
 */

namespace SentrySocial;

use Config;
use Input;

/**
 * Oauth2 Driver Class
 *
 * @package  SentrySocial
 * @author   Daniel Petrie
 */
class Driver_OAuth2 extends SentrySocial
{
	/**
	 * Authenticate
	 */
	public function authenticate()
	{
		// set callback
		$callback_url = \URL::base().'/'.str_finish(Config::get('sentrysocial::sentrysocial.callback_url'), '/').$this->provider->name;
		$this->provider->callback = $callback_url;

		// authorize
		return \Redirect::to($this->provider->authorize(array(
			'redirect_uri' => $this->provider->callback
		)));
	}

	/**
	 * Callback
	 *
	 * @return  object  provider token object
	 */
	public function callback()
	{
		return $this->provider->access(Input::get('code'));
	}

	/**
	 * Get User Information
	 *
	 * @param   object  provider access token object
	 * @return  array   user information
	 */
	 public function get_user_info($token)
	 {
	 	return $this->provider->get_user_info($token);
	 }

}