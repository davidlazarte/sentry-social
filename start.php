<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * @package    Sentry Social
 * @version    1.1
 * @author     Cartalyst LLC
 * @license    http://getplatform.com/manuals/sentrysocial/license
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

// Autoload classes
Autoloader::namespaces(array(
    'SentrySocial' => Bundle::path('sentrysocial'),
));

// map the auth controller to extend
Autoloader::map(array(
	'SentrySocial_Auth_Controller' => __DIR__.DS.'controllers/auth.php',
));

Autoloader::alias('SentrySocial\\SentrySocial', 'SentrySocial');
Autoloader::alias('SentrySocial\\SentrySocialException', 'SentrySocialException');