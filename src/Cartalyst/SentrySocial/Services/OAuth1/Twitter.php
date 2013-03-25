<?php namespace Cartalyst\SentrySocial\Services\OAuth1;
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

use Cartalyst\SentrySocial\Services\ServiceInterface;
use OAuth\OAuth1\Service\Facebook as BaseService;

class Facebook extends BaseService implements ServiceInterface {

	/**
	 * Array of cached user info.
	 *
	 * @var array
	 */
	protected $cachedInfo = array();

	/**
	 * Returns the user's unique identifier on the service.
	 *
	 * @return mixed
	 */
	public function getUniqueIdentifier()
	{
		$info = $this->getUserInfo();
		return $info['id'];
	}

	/**
	 * Returns the user's email address. Note, some services
	 * do not provide this in which case "null" is returned.
	 *
	 * @return string|null
	 */
	public function getEmail()
	{
		return null;
	}

	/**
	 * Returns the user's name. If first / last name can be
	 * determined, an array is returned. If not, a string is
	 * returned. If it cannot be determined, "null" is returned.
	 *
	 * @return array|string|null
	 */
	public function getName()
	{
		$info = $this->getUserInfo();
		return $info['name'];
	}

	/**
	 * Retuns an array of basic user information.
	 *
	 * @return array
	 * @link   https://dev.twitter.com/docs/api/1.1/get/friendships/lookup
	 */
	public function getUserInfo()
	{
		if (empty($this->cachedInfo))
		{
			$this->cachedInfo = reset(json_decode($this->request('users/lookup.json', true));
		}

		return $this->cachedInfo;
	}

}
