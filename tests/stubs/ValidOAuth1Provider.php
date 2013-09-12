<?php

use League\OAuth1\Client\Credentials\TokenCredentials;

class ValidOAuth1Provider extends League\OAuth1\Client\Server\Server {

	/**
	 * Get the URL for retrieving temporary credentials.
	 *
	 * @return string
	 */
	public function urlTemporaryCredentials() {}

	/**
	 * Get the URL for redirecting the resource owner to authorize the client.
	 *
	 * @return string
	 */
	public function urlAuthorization() {}

	/**
	 * Get the URL retrieving token credentials.
	 *
	 * @return string
	 */
	public function urlTokenCredentials() {}

	/**
	 * Get the URL for retrieving user details.
	 *
	 * @return string
	 */
	public function urlUserDetails() {}

	/**
	 * Take the decoded data from the user details URL and convert
	 * it to a User object.
	 *
	 * @param  mixed  $data
	 * @param  TokenCredentials  $tokenCredentials
	 * @return User
	 */
	public function userDetails($data, TokenCredentials $tokenCredentials) {}

}
