<?php
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

return array(

	/*
	|--------------------------------------------------------------------------
	| Storage Driver
	|--------------------------------------------------------------------------
	|
	| Specify the storage engine used by Sentry Social.
	|
	| Supported: "eloquent", "session", "memory"
	|
	*/

	'storage' => 'eloquent',

	/*
	|--------------------------------------------------------------------------
	| Storage Model
	|--------------------------------------------------------------------------
	|
	| When using the "eloquent" storage driver, please specify the model name
	| used for the storage model.
	|
	*/

	'model' => 'Cartalyst\SentrySocial\Users\Eloquent\Service',

);
