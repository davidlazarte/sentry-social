<?php
/**
 * OAuth Request Token
 *
 * @package    Kohana/OAuth
 * @category   Token
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

class Libraries_OAuth_Token_Request extends Libraries_OAuth_Token {

	protected $name = 'request';

	/**
	 * @var  string  request token verifier
	 */
	protected $verifier;

	/**
	 * Change the token verifier.
	 *
	 *     $token->verifier($key);
	 *
	 * @param   string   new verifier
	 * @return  $this
	 */
	public function verifier($verifier)
	{
		$this->verifier = $verifier;

		return $this;
	}

} // End Token_Request
