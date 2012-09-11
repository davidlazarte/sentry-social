<?php
/**
 * OAuth2 Token
 *
 * @package    FuelPHP/OAuth2
 * @category   Token
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 *
 * @modified_by  Cartalyst LLC
 * @copyright    (c) 2012 Cartalyst LLC.
 * @version      1.1
 */

namespace SentrySocial;

abstract class Libraries_OAuth2_Token {

	/**
	 * Create a new token object.
	 *
	 *     $token = Token::make('access', $name);
	 *
	 * @param   string  token type
	 * @param   array   token options
	 * @return  Token
	 */
	public static function make($type = 'access', array $options = null)
	{
		$class = 'SentrySocial\\Libraries_OAuth2_Token_'.ucfirst(str_replace('-', '_', $type));

		return new $class($options);
	}

	/**
	 * Return the value of any protected class variable.
	 *
	 *     // Get the token secret
	 *     $secret = $token->secret;
	 *
	 * @param   string  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Return a boolean if the property is set
	 *
	 *     // Get the token secret
	 *     if ($token->secret) exit('YAY SECRET');
	 *
	 * @param   string  variable name
	 * @return  bool
	 */
	public function __isset($key)
	{
		return isset($this->$key);
	}

	/**
	 * Returns the token key.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return (string) $this->access_token;
	}

} // End Token
