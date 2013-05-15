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
	 * @param mixed $data
	 * @param array $headers
	 * @param string $method
	 * @return string
	 * @throws TokenResponseException
	 */
	public function retrieveResponse(UriInterface $endpoint, $data, array $headers = array(), $method = 'POST')
	{
		$uri          = $endpoint->getAbsoluteUri();
		$headers = array_merge(array(
			'Content-type' => 'application/x-www-form-urlencoded',
			'Host'         => $endpoint->getHost(),
			'Connection'   => 'close',
		), $headers);

		if (is_array($data))
		{
			$data = http_build_query($data);
		}

		switch ($method)
		{
			case 'post':
			case 'put':
			case 'patch':
				$response = Requests::request($uri, $headers, $data, strtoupper($method), $options);
				break;

			default:
				$response = Requests::request($uri, $headers, null, strtoupper($method));
				break;
		}

		// We'll throw an Exception if we didn't get a
		// good response which should allow the dev
		// to learn what went wrong rather than
		// fail silently.
		try
		{
			$response->throw_for_status();
		}
		catch (\Exception $e)
		{
			if (isset($headers['Authorization']))
			{
				echo '<pre>'.print_r($headers['Authorization'], true).'</pre>';
			}
			dd([$uri, $headers, $data, strtoupper($method)], $response);
			throw $e;
		}

		return $response->body;
	}

}
