<?php
/**
 * OAuth Signature
 *
 * @package    Kohana/OAuth
 * @category   Signature
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 *
 * @modified_by  Cartalyst LLC
 * @copyright    (c) 2012 Cartalyst LLC.
 * @version      1.1
 */

namespace SentrySocial;

abstract class Libraries_OAuth_Signature {

	/**
	 * Create a new signature object by name.
	 *
	 *     $signature = Signature::make('HMAC-SHA1');
	 *
	 * @param   string  signature name: HMAC-SHA1, PLAINTEXT, etc
	 * @param   array   signature options
	 * @return  Signature
	 */
	public static function make($name, array $options = NULL)
	{
		// Create the class name as a base of this class
		$class = 'SentrySocial\\Libraries_OAuth_Signature_'.str_replace('-', '_', $name);

		return new $class($options);
	}

	/**
	 * @var  string  signature name: HMAC-SHA1, PLAINTEXT, etc
	 */
	protected $name;

	/**
	 * Return the value of any protected class variables.
	 *
	 *     $name = $signature->name;
	 *
	 * @param   string  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Get a signing key from a consumer and token.
	 *
	 *     $key = $signature->key($consumer, $token);
	 *
	 * [!!] This method implements the signing key of [OAuth 1.0 Spec 9](http://oauth.net/core/1.0/#rfc.section.9).
	 *
	 * @param   Consumer  consumer
	 * @param   Token     token
	 * @return  string
	 * @uses    OAuth::urlencode
	 */
	public function key(Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token = NULL)
	{
		$key = Libraries_OAuth_OAuth::urlencode($consumer->secret).'&';

		if ($token)
		{
			$key .= Libraries_OAuth_OAuth::urlencode($token->secret);
		}

		return $key;
	}

	abstract public function sign(Libraries_OAuth_Request $request, Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token = NULL);

	abstract public function verify($signature, Libraries_OAuth_Request $request, Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token = NULL);

} // End Signature
