<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * @package    Sentry Social
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    http://getplatform.com/manuals/sentrysocial/license
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

// Autoload classes
Autoloader::namespaces(array(
    'SentrySocial' => Bundle::path('sentrysocial'),
));

Autoloader::alias('SentrySocial\\SentrySocial', 'SentrySocial');
Autoloader::alias('SentrySocial\\SentrySocialException', 'SentrySocialException');

Autoloader::map(array(
	/**
	 * OAuth
	 */
	'SentrySocial\\OAuth\\OAuth'     => __DIR__.DS.'libraries'.DS.'oauth'.DS.'oauth.php',
	'SentrySocial\\OAuth\\Exception' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'oauth.php',

	'SentrySocial\\OAuth\\Consumer' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'consumer.php',
	'SentrySocial\\OAuth\\Provider' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider.php',

	'SentrySocial\\OAuth\\Provider_Dropbox'  => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'dropbox.php',
	'SentrySocial\\OAuth\\Provider_Flickr'   => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'flickr.php',
	'SentrySocial\\OAuth\\Provider_Google'   => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'google.php',
	'SentrySocial\\OAuth\\Provider_Linkedin' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'linkedin.php',
	'SentrySocial\\OAuth\\Provider_Tumblr'   => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'tumblr.php',
	'SentrySocial\\OAuth\\Provider_Twitter'  => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'twitter.php',
	'SentrySocial\\OAuth\\Provider_Youtube'  => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'youtube.php',
	'SentrySocial\\OAuth\\Provider_Vimeo'    => __DIR__.DS.'libraries'.DS.'oauth'.DS.'provider'.DS.'vimeo.php',

	'SentrySocial\\OAuth\\Request' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'request.php',

	'SentrySocial\\OAuth\\Request_Access'    => __DIR__.DS.'libraries'.DS.'oauth'.DS.'request'.DS.'access.php',
	'SentrySocial\\OAuth\\Request_Authorize' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'request'.DS.'authorize.php',
	'SentrySocial\\OAuth\\Request_Resource'  => __DIR__.DS.'libraries'.DS.'oauth'.DS.'request'.DS.'resource.php',
	'SentrySocial\\OAuth\\Request_Token'     => __DIR__.DS.'libraries'.DS.'oauth'.DS.'request'.DS.'token.php',

	'SentrySocial\\OAuth\\Response'      => __DIR__.DS.'libraries'.DS.'oauth'.DS.'response.php',
	'SentrySocial\\OAuth\\Server'        => __DIR__.DS.'libraries'.DS.'oauth'.DS.'server.php',
	'SentrySocial\\OAuth\\Signature'     => __DIR__.DS.'libraries'.DS.'oauth'.DS.'signature.php',
	'SentrySocial\\OAuth\\Token'         => __DIR__.DS.'libraries'.DS.'oauth'.DS.'token.php',
	'SentrySocial\\OAuth\\Token_Access'  => __DIR__.DS.'libraries'.DS.'oauth'.DS.'token'.DS.'access.php',
	'SentrySocial\\OAuth\\Token_Request' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'token'.DS.'request.php',

	'SentrySocial\\OAuth\\Signature_HMAC_SHA1' => __DIR__.DS.'libraries'.DS.'oauth'.DS.'signature'.DS.'hmac'.DS.'sha1.php',

	/**
	 * OAuth2
	 */
	'SentrySocial\\OAuth2\\Exception' > __DIR__.DS.'libraries'.DS.'oauth2'.DS.'exception.php',

	'SentrySocial\\OAuth2\\Model_Server'       => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'model'.DS.'server.php',
	'SentrySocial\\OAuth2\\Model_Server_Db'    => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'model'.DS.'server'.DS.'db.php',
	'SentrySocial\\OAuth2\\Model_Server_Mongo' => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'model'.DS.'server'.DS.'mongo.php',

	'SentrySocial\\OAuth2\\Provider'             => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider.php',
	'SentrySocial\\OAuth2\\Provider_Blooie'      => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'blooie.php',
	'SentrySocial\\OAuth2\\Provider_Facebook'    => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'facebook.php',
	'SentrySocial\\OAuth2\\Provider_Foursquare'  => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'foursquare.php',
	'SentrySocial\\OAuth2\\Provider_Google'      => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'google.php',
	'SentrySocial\\OAuth2\\Provider_Github'      => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'github.php',
	'SentrySocial\\OAuth2\\Provider_Instagram'   => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'instagram.php',
	'SentrySocial\\OAuth2\\Provider_Paypal'      => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'paypal.php',
	'SentrySocial\\OAuth2\\Provider_Soundcloud'  => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'soundcloud.php',
	'SentrySocial\\OAuth2\\Provider_Windowslive' => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'provider'.DS.'windowslive.php',

	'SentrySocial\\OAuth2\\Request' => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'request.php',
	'SentrySocial\\OAuth2\\Server'  => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'server.php',

	'SentrySocial\\OAuth2\\Token'           => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'token.php',
	'SentrySocial\\OAuth2\\Token_Access'    => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'token'.DS.'access.php',
	'SentrySocial\\OAuth2\\Token_Authorize' => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'token'.DS.'authorize.php',
	'SentrySocial\\OAuth2\\Token_Refresh'   => __DIR__.DS.'libraries'.DS.'oauth2'.DS.'token'.DS.'refresh.php',
));