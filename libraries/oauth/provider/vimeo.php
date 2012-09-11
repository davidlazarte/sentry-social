<?php
/**
 * OAuth Vimeo Provider
 *
 * Documents for implementing Vimeo OAuth can be found at
 * <http://vimeo.com/api/docs/oauth>.
 *
 * [!!] This class does not implement the Vimeo API. It is only an
 * implementation of standard OAuth with Vimeo as the service provider.
 *
 * @package    OAuth
 * @author     Fuel Development Team
 *
 * @modified_by  Cartalyst LLC
 * @copyright    (c) 2012 Cartalyst LLC.
 * @version      1.1
 */

namespace SentrySocial;

class Libraries_OAuth_Provider_Vimeo extends Libraries_OAuth_Provider {

	public $name = 'vimeo';

	public function url_request_token()
	{
		return 'http://vimeo.com/oauth/request_token';
	}

	public function url_authorize()
	{
		return 'http://vimeo.com/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'http://vimeo.com/oauth/access_token';
	}

	public function get_user_info(Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token)
	{
		// Create a new GET request with the required parameters
		$request = Libraries_OAuth_Request::make('resource', 'GET', 'http://vimeo.com/api/rest/v2/', array(
			'method'             => 'vimeo.people.getInfo',
			'oauth_consumer_key' => $consumer->key,
			'oauth_token'        => $token->access_token,
			'format'             => 'json',
		));

		// Sign the request using the consumer and token
		$request->sign($this->signature, $consumer, $token);

		$response = json_decode($request->execute());
		$user = $response->person;

		$profile_image = end($user->portraits->portrait);
		$url = current($user->url);

		// Create a response from the request
		return array(
			'uid'         => $user->id,
			'nickname'    => $user->username,
			'name'        => $user->display_name ?: $user->username,
			'location'    => $user->location,
			'image'       => $profile_image->_content,
			'description' => $user->bio,
			'urls'        => array(
			  'Website' => $url,
			  'Vimeo'   => $user->profileurl,
			),
		);
	}

} // End Provider_Vimeo