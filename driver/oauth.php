<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst Software Licence.
 *
 * @package    Sentry Social
 * @version    1.1
 * @author     Cartalyst LLC
 * @license    Cartalyst Software Licence - http://cartalyst.com/licence
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace SentrySocial;

use Config;
use Cookie;
use Input;
use Response;

/**
 * OAuth Driver Class
 *
 * @package  SentrySocial
 */
class Driver_OAuth extends SentrySocial
{
	/**
	 * @var  object  OAuth consumer object
	 */
	protected $consumer = null;

	/**
	 * Authenticate user
	 */
	public function authenticate()
	{
		$config = Config::get('sentrysocial::sentrysocial.providers.'.$this->provider->name);

		// set consumer
		$this->consumer = Libraries_OAuth_Consumer::make(array(
			'key'    => $config['app_id'],
			'secret' => $config['app_secret']
		));

		// set the callback url
		$callback_url = \URL::base().'/'.str_finish(Config::get('sentrysocial::sentrysocial.url.callback'), '/').$this->provider->name;

		$this->consumer->callback($callback_url);

		// Get a request token for the consumer
		$token = $this->provider->request_token($this->consumer);
		\Cookie::put('sentry_social_token', base64_encode(serialize($token)));

		// Redirect to provider login page

		return \Redirect::to($this->provider->authorize_url($token, array(
			'oauth_callback' => $callback_url,
		)));
	}

	/**
	 * Callback
	 *
	 * @return  object  provider token object
	 * @throws  SentrySocialException
	 */
	public function callback()
	{
		$denied = Input::get('denied');

		if ($denied)
		{
			return \Redirect::to(Config::get('sentrysocial::sentrysocial.url.login'))->send();
		}

		$config = Config::get('sentrysocial::sentrysocial.providers.'.$this->provider->name);

		// set consumer
		$this->consumer = Libraries_OAuth_Consumer::make(array(
			'key'    => $config['app_id'],
			'secret' => $config['app_secret']
		));

		// get token if it is set
		if ($token = Cookie::get('sentry_social_token'))
		{
			Cookie::forget('sentry_social_token');

			// Get the token from storage
			$token = unserialize(base64_decode($token));

			// make sure token matches
			if ($token->access_token != Input::get('oauth_token'))
			{
				throw new SentrySocialException('Invalid Token in Callback');
			}
		}

		// set the verifier
		$token->verifier(Input::get('oauth_verifier'));

		// return token
		return $this->provider->access_token($this->consumer, $token);
	}

	/**
	 * Get User Information
	 *
	 * @return  array  user information
	 */
	 public function get_user_info($token)
	 {
	 	return $this->provider->get_user_info($this->consumer, $token);
	 }

}
