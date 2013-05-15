<?php namespace Cartalyst\SentrySocial\HttpClients;
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

use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use Requests;

class RequestsClient implements ClientInterface {

	/**
	 * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
	 * They should return, in string form, the response body and throw an exception on error.
	 *
	 * @abstract
	 * @param UriInterface $endpoint
	 * @param mixed $requestBody
	 * @param array $extraHeaders
	 * @param string $method
	 * @return string
	 * @throws TokenResponseException
	 */
	public function retrieveResponse(UriInterface $endpoint, $requestBody, array $extraHeaders = array(), $method = 'POST')
	{
		$uri     = $endpoint->getAbsoluteUri();
		$headers = array();
		$options = array();

		switch ($method)
		{
			case 'post':
			case 'put':
			case 'patch':
				$request = Requests::create($uri, $headers, $options);
				break;

			default:
				$request = Requests::create($uri, $headers, $requestBody, $options);
				break;
		}

		var_dump($request);
	}

}
