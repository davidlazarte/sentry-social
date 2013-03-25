<?php namespace Cartalyst\SentrySocial\Users\Eloquent;
/**
 * Part of the Sentry package.
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

use Cartalyst\Sentry\Users\Eloquent\User as BaseUser;

class User extends BaseUser {

	/**
	 * Returns the services which we have access keys for, for the user.
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function services()
	{
		return $this->hasMany('Cartalyst\SentrySocial\Users\Eloquent\Service', 'user_id');
	}

}
