<?php namespace Cartalyst\SentrySocial\SocialLinks\Eloquent;
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
	 * Finds the a social link object by the service name
	 * and user's unique identifier.
	 *
	 * @param  string  $serviceName
	 * @param  string  $userUniqueIdentifier
	 * @return Cartalyst\SentrSocial\SocialLinks\LinkInterface
	 */
	public function findLink(ServiceInterface $service)
	{
		$serviceName = $service->getServiceName();
		$uid         = $service->getUserUniqueIdentifier();

		$query = $this
			->createModel()
			->newQuery()
			->where('service', '=', $serviceName)
			->where('uid', '=', $uid);

		if ( ! $link = $query->first())
		{
			$link = $this->createModel();
			$link->service = $serviceName;
			$link->uid     = $uid;
		}

		$this->storeToken($service, $link);

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

	protected function storeToken(ServiceInterface $service, LinkInterface $link)
	{
		$token = $service->getStorage()->retrieveAccessToken();

		$link->access_token = $token->getAccessToken();
		$link->end_of_life  = $token->getEndOfLife();
		$link->extra_params = $token->getExtraParams();

		if ($token instanceof OAuth1TokenInterface)
		{
			$link->access_token_secret = $token->getAccessTokenSecret();
			$link->request_token = $token->getRequestToken();
			$link->request_token_secret = $token->getRequestTokenSecret();
		}
		else
		{
			$link->refresh_token = $token->getRefreshToken();
		}
	}

}
