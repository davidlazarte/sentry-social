<?php namespace Cartalyst\SentrySocial\RequestProviders;
/**
 * Part of the Data Grid package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Data Grid
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class NativeProvider implements ProviderInterface {

	public function getOAuth1TemporaryCredentialsIdentifier()
	{
		return isset($_GET['oauth_token']) ? $_GET['oauth_token'] : null;
	}

	public function getOAuth1Verifier()
	{
		return isset($_GET['verifier']) ? $_GET['oauth_verifier'] : null;
	}

	public function getOAuth2Code()
	{
		return isset($_GET['code']) ? $_GET['code'] : null;
	}

}
