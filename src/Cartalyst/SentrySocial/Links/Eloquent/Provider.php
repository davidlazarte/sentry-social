<?php namespace Cartalyst\SentrySocial\Links\Eloquent;
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
use Cartalyst\SentrySocial\SocialLinks\LinkInterface;
use Cartalyst\SentrySocial\SocialLinks\ProviderInterface;
use OAuth\OAuth1\Token\TokenInterface as OAuth1TokenInterface;
use OAuth\Oauth2\Token\TokenInterface as OAuth2TokenInterface;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent social model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\SentrySocial\SocialLinks\Eloquent\SocialLink';

	/**
	 * Create a new Eloquent Social Link provider.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * Finds a link (or creates one) for the given
	 * provider slug and provider instance.
	 *
	 * @param  string  $slug
	 * @param  mixed   $provider
	 * @return \Cartalyst\SentrySocial\Links\LinkInterface
	 */
	public function findLink($slug, $provider)
	{
		$uid = $provider->getUserUid();

		$query = $this
			->createModel()
			->newQuery()
			->where('provider', '=', $slug)
			->where('uid', '=', $uid);

		if ( ! $link = $query->first())
		{
			$link = $this->createModel();
			$link->provider = $slug;
			$link->uid      = $uid;
		}

		$link->save();

		return $link;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
