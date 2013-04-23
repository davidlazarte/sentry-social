<?php namespace Cartalyst\SentrySocial\Controllers;
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

use App;
use Config;
use Illuminate\Routing\Controllers\Controller;
use Input;
use URL;
use Redirect;
use SentrySocial;

class OAuthController extends Controller {

	public function getIndex()
	{
		foreach (Config::get('cartalyst/sentry-social::services.connections') as $service => $config)
		{
			echo "<p><a href=\"".URL::to("oauth/authorize/{$service}")."\">Connect with [{$service}]</a></p>";
		}
	}

	/**
	 * Shows a link to authenticate a service.
	 *
	 * @param  string  $service
	 * @return string
	 */
	public function getAuthorize($service)
	{
		$service = SentrySocial::make($service, URL::to("oauth/callback/{$service}"));

		return Redirect::to((string) $service->getAuthorizationUri());
	}

	/**
	 * Handles authentication
	 */
	public function getCallback($service)
	{
		$service = SentrySocial::make($service, URL::to("oauth/callback/{$service}"));

		// If we have an access code
		if ($code = Input::get('code'))
		{
			try
			{
				// Hmm, not set on this syntax.
				$user = SentrySocial::authenticate($service, $code);
			}

			// Some providers (e.g. Twitter) won't give an email
			// address.
			catch (LoginRequiredException $e)
			{

			}
		}
		else
		{
			App::abort(404);
		}
	}

}
