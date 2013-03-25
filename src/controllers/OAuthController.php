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

use Illuminate\Routing\Controllers\Controller;

class OAuthController extends Controller {

	/**
	 * Shows a link to authenticate a service.
	 *
	 * @param  string  $service
	 * @return string
	 */
	public function indexAction($service)
	{
		$url = \URL::to(\Request::getPathInfo(), array('go' => 'go'));
		return "<a href=\"{$url}\">Login with {$service}</a>";
	}

	/**
	 * Handles authentication
	 */
	public function authenticateAction($service)
	{
		$service = \SentrySocial::make($service);

		// If we have an access code
		if ($code = \Request::input('code'))
		{
			try
			{
				// Hmm, not set on this syntax.
				$user = \SentrySocial::authenticate($service, $code);
			}

			// Some providers (e.g. Twitter) won't give an email
			// address.
			catch (LoginRequiredException $e)
			{

			}
		}
		elseif (\Request::input('go') == 'go')
		{
			return \Redirect::to($service->getAuthorizationUri());
		}
		else
		{
			\App::abort(404);
		}
	}

}
