<?php namespace Cartalyst\SentrySocial\Storage;
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

use OAuth\Common\Storage\TokenStorageInterface;

class Eloquent implements TokenStorageInterface {

	/**
	 * The service model we will use for our storage.
	 *
	 * @var string
	 */
	protected $model;

	public function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * Retrieves the given access token
	 *
	 * @return \OAuth\Common\Token\TokenInterface
	 */
	public function retrieveAccessToken()
	{

	}

	/**
	 * @param \OAuth\Common\Token\TokenInterface $token
	 */
	public function storeAccessToken(TokenInterface $token)
	{

	}

	/**
	 * @return bool
	 */
	public function hasAccessToken()
	{

	}

	/**
	* Delete the users token. Aka, log out.
	*/
	public function clearToken()
	{

	}

}
