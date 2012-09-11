<?php
/**
 * OAuth Provider
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

use Inflector;
use Response;
use Session;

abstract class Libraries_OAuth2_Provider
{
	/**
	 * Create a new provider.
	 *
	 *     // Load the Twitter provider
	 *     $provider = OAuth_Provider::make('twitter');
	 *
	 * @param   string   provider name
	 * @param   array    provider options
	 * @return  OAuth_Provider
	 */
	public static function make($name, array $options = null)
	{
		$class = 'SentrySocial\\Libraries_OAuth2_Provider_'.ucfirst(str_replace('-', '_', $name));
		return new $class($options);
	}

	/**
	 * @var  string  provider name
	 */
	public $name;

	/**
	 * @var  string  uid key name
	 */
	public $uid_key = 'uid';

	/**
	 * @var  string  additional request parameters to be used for remote requests
	 */
	public $callback = null;

	/**
	 * @var  array  additional request parameters to be used for remote requests
	 */
	protected $params = array();

	/**
	 * @var  string  the method to use when requesting tokens
	 */
	protected $method = 'GET';

	/**
	 * @var  string  default scope (useful if a scope is required for user info)
	 */
	protected $scope;

	/**
	 * @var  string  scope separator, most use "," but some like Google are spaces
	 */
	protected $scope_seperator = ',';

	/**
	 * Overloads default class properties from the options.
	 *
	 * Any of the provider options can be set here, such as app_id or secret.
	 *
	 * @param   array   provider options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		if ( ! $this->name)
		{
			// Attempt to guess the name from the class name
			$this->name = strtolower(substr(get_class($this), strlen('SentrySocial\\Libraries_OAuth2_Provider_')));
		}

		if ( ! $this->client_id = array_get($options, 'id'))
		{
			throw new Libraries_OAuth2_Exception(array('message' => 'Required option not provided: id'));
		}

		$this->callback = array_get($options, 'callback');
		$this->client_secret = array_get($options, 'secret');
		$this->scope = array_get($options, 'scope');

		$this->redirect_uri = \URL::current();
	}

	/**
	 * Return the value of any protected class variable.
	 *
	 *     // Get the provider signature
	 *     $signature = $provider->signature;
	 *
	 * @param   string  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Returns the authorization URL for the provider.
	 *
	 *     $url = $provider->url_authorize();
	 *
	 * @return  string
	 */
	abstract public function url_authorize();

	/**
	 * Returns the access token endpoint for the provider.
	 *
	 *     $url = $provider->url_access_token();
	 *
	 * @return  string
	 */
	abstract public function url_access_token();

	/*
	* Get an authorization code from Facebook.  Redirects to Facebook, which this redirects back to the app using the redirect address you've set.
	*/
	public function authorize($options = array())
	{
		$state = md5(uniqid(rand(), TRUE));
		Session::put('state', $state);

		return $this->url_authorize().'?'.http_build_query(array(
			'client_id' 		=> $this->client_id,
			'redirect_uri' 		=> array_get($options, 'redirect_uri', $this->redirect_uri),
			'state' 			=> $state,
			'scope'     		=> is_array($this->scope) ? implode($this->scope_seperator, $this->scope) : $this->scope,
			'response_type' 	=> 'code',
		));
	}

	/*
	* Get access to the API
	*
	* @param	string	The access code
	* @return	object	Success or failure along with the response details
	*/
	public function access($code, $options = array())
	{
		$params = array(
			'client_id' 	=> $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type' 	=> array_get($options, 'grant_type', 'authorization_code'),
		);

		switch ($params['grant_type'])
		{
			case 'authorization_code':
				$params['code'] = $code;
				$params['redirect_uri'] = array_get($options, 'redirect_uri', $this->redirect_uri);
			break;

			case 'refresh_token':
				$params['refresh_token'] = $code;
			break;
		}

		$response = null;
		$url = $this->url_access_token();

		// Get ready to make a request
		// $request = \Request::make($url, 'curl');
		//
		// $request->set_params($params);

		switch ($this->method)
		{
			case 'GET':

				// Need to switch to Request library, but need to test it on one that works
				$url .= '?'.http_build_query($params);
				$response = file_get_contents($url);

				parse_str($response, $return);

			break;

			case 'POST':

				$postdata = http_build_query($params);
				$opts = array(
					'http' => array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata
					)
				);
				$context  = stream_context_create($opts);
				$response = file_get_contents($url, false, $context);

				$return = get_object_vars(json_decode($response));

				/*
				Need to convert to curl eventually
				try
				{
					$request->set_header('Accept', 'application/json');
					$request->set_method('POST');
					$request = $request->execute();
				}
				catch (RequestException $e)
				{
					\Debug::dump($request->response());
					exit;
				}
				catch (HttpNotFoundException $e)
				{
					\Debug::dump($request->response());
					exit;
				}

				$response = $request->response();

				logger(\Fuel::L_INFO, 'Access token response: '.print_r($body, true), __METHOD__);

				// Try to get the actual response, its hopefully an array
				$body = $response->body();
				*/

			break;

			default:
				throw new \OutOfBoundsException("Method '{$this->method}' must be either GET or POST");
		}

		if (isset($return['error']))
		{
			throw new Libraries_OAuth2_Exception($return);
		}


		return Libraries_OAuth2_Token::make('access', $return);

	}

}
