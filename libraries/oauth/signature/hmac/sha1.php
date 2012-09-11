<?php
/**
 * The HMAC-SHA1 signature provides secure signing using the HMAC-SHA1
 * algorithm as defined by [RFC2104](http://tools.ietf.org/html/rfc2104).
 * It uses [Request::base_string] as the text and [Signature::key]
 * as the signing key.
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

class Libraries_OAuth_Signature_HMAC_SHA1 extends Libraries_OAuth_Signature {

	protected $name = 'HMAC-SHA1';

	/**
	 * Generate a signed hash of the base string using the consumer and token
	 * as the signing key.
	 *
	 *     $sig = $signature->sign($request, $consumer, $token);
	 *
	 * [!!] This method implements [OAuth 1.0 Spec 9.2.1](http://oauth.net/core/1.0/#rfc.section.9.2.1).
	 *
	 * @param   Request   request
	 * @param   Consumer  consumer
	 * @param   Token     token
	 * @return  string
	 * @uses    Signature::key
	 * @uses    Request::base_string
	 */
	public function sign(Libraries_OAuth_Request $request, Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token = NULL)
	{
		// Get the signing key
		$key = $this->key($consumer, $token);

		// Get the base string for the signature
		$base_string = $request->base_string();

		// Sign the base string using the key
		return base64_encode(hash_hmac('sha1', $base_string, $key, TRUE));
	}

	/**
	 * Verify a HMAC-SHA1 signature.
	 *
	 *     if ( ! $signature->verify($signature, $request, $consumer, $token))
	 *     {
	 *         throw new Exception('Failed to verify signature');
	 *     }
	 *
	 * [!!] This method implements [OAuth 1.0 Spec 9.2.2](http://oauth.net/core/1.0/#rfc.section.9.2.2).
	 *
	 * @param   string          signature to verify
	 * @param   Request   request
	 * @param   Consumer  consumer
	 * @param   Token     token
	 * @return  boolean
	 * @uses    Signature_HMAC_SHA1::sign
	 */
	public function verify($signature, Libraries_OAuth_Request $request, Libraries_OAuth_Consumer $consumer, Libraries_OAuth_Token $token = NULL)
	{
		return $signature === $this->sign($request, $consumer, $token);
	}

} // End Signature_HMAC_SHA1
