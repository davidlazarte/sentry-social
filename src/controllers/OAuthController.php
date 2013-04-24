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
use Sentry;
use SentrySocial;

class OAuthController extends Controller {

	/**
	 * Lists all available services to authenticate with.
	 *
	 * @return Illuminate\View\View
	 */
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
	 * @param  string  $serviceName
	 * @return string
	 */
	public function getAuthorize($serviceName)
	{
		$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));

		return Redirect::to((string) $service->getAuthorizationUri());
	}

	/**
	 * Handles authentication
	 *
	 * @param  string  $serviceName
	 * @return mixed
	 */
	public function getCallback($serviceName)
	{
		$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));

		// If we have an access code
		if ($code = Input::get('code'))
		{
			if (SentrySocial::authenticate($service, $code))
			{
				return Redirect::to('oauth/authenticated');
			}
		}

		App::abort(404);
	}

	/**
	 * Returns the "authenticated" view which simply shows the
	 * authenticated user.
	 *
	 * @return mixed
	 */
	public function getAuthenticated()
	{
		if ( ! Sentry::check())
		{
			App::abort(403, 'Not Authenticated');
		}

		$response = <<<RESPONSE
<h1>Authenticated!</h1>
<p>You have successfully authenticated. Your user details are below:</p>
<pre>%s</pre>
RESPONSE;

		return sprintf($response, print_r(Sentry::getUser(), true));
	}

}
