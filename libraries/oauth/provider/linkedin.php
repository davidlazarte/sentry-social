<?php
/**
 * OAuth Twitter Provider
 *
 * Documents for implementing Twitter OAuth can be found at
 * <http://dev.twitter.com/pages/auth>.
 *
 * [!!] This class does not implement the Twitter API. It is only an
 * implementation of standard OAuth with Twitter as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 *
 * @modified_by  Cartalyst LLC
 * @copyright    (c) 2012 Cartalyst LLC.
 * @version      1.1
 */

namespace SentrySocial;

class Libraries_OAuth_Provider_Linkedin extends Libraries_OAuth_Provider {

	public $name = 'linkedin';

	public function url_request_token()
	{
		return 'https://api.linkedin.com/uas/oauth/requestToken';
	}

	public function url_authorize()
	{
		return 'https://api.linkedin.com/uas/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'https://api.linkedin.com/uas/oauth/accessToken';
	}

	public function get_user_info(Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token)
	{
		// Create a new GET request with the required parameters
		$url = 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,member-url-resources,picture-url,location,public-profile-url)?format=json';
		$request = Libraries_OAuth_Request::make('resource', 'GET', $url, array(
			'oauth_consumer_key' => $consumer->key,
			'oauth_token'        => $token->access_token,
		));

		// Sign the request using the consumer and token
		$request->sign($this->signature, $consumer, $token);

		// Gets the JSON object from the response
		$user = json_decode($request->execute()->body, true);

		// Split the profile url to get the user's nickname
		$split_url = explode('/', $user['publicProfileUrl']);

		// Create a response from the request
		return array(
			'uid'         => $user['id'],
			'name'        => $user['firstName'].' '.$user['lastName'],
			'image'       => $user['pictureUrl'],
			'nickname'    => end($split_url),
			'description' => $user['headline'],
			'location'    => array_get($user, 'location.name'),
			'urls'        => array(
				'Linked In' => $user['publicProfileUrl'],
			),
		);
	}

} // End Provider_Dropbox