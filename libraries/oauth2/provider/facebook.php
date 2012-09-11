<?php
/**
 * Facebook OAuth2 Provider
 *
 * @package    FuelPHP/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 *
 * @modified_by  Cartalyst LLC
 * @copyright    (c) 2012 Cartalyst LLC.
 * @version      1.1
 */

namespace SentrySocial;

class Libraries_OAuth2_Provider_Facebook extends Libraries_OAuth2_Provider
{
	public $scope = array('offline_access', 'email', 'read_stream');

	public function url_authorize()
	{
		return 'https://www.facebook.com/dialog/oauth';
	}

	public function url_access_token()
	{
		return 'https://graph.facebook.com/oauth/access_token';
	}

	public function get_user_info(Libraries_OAuth2_Token_Access $token)
	{
		$url = 'https://graph.facebook.com/me?'.http_build_query(array(
			'access_token' => $token->access_token,
		));

		$user = json_decode(file_get_contents($url));

		// Create a response from the request
		return array(
			'uid'      => $user->id,
			'name'     => $user->name,
			'nickname' => isset($user->username) ? $user->username : null,
			'email'    => isset($user->email) ? $user->email : null,
			'image'    => 'https://graph.facebook.com/me/picture?type=normal&access_token='.$token->access_token,
			'urls'     => array(
				'Facebook' => $user->link,
			),
		);
	}
}
