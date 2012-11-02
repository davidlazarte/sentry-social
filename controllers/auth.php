<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst Software Licence.
 *
 * @package    Sentry Social
 * @version    1.1
 * @author     Cartalyst LLC
 * @license    Cartalyst Software Licence - http://cartalyst.com/licence
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class SentrySocial_Auth_Controller extends Controller
{
	public $restful = true;

	public function get_session($provider)
	{
		return SentrySocial::make($provider)->authenticate();
	}

	public function get_callback($provider)
	{
		$status = SentrySocial::make($provider)->login();

		switch ($status)
		{
			// user needs to register their email address, since we don't have it
			case 'register':
				return Redirect::to(Config::get('sentrysocial::sentrysocial.url.register'));
				break;

			// we have everything, redirect the user wherever you want now that they are signed in
			case 'authenticated':
				return Redirect::to(Config::get('sentrysocial::sentrysocial.url.authenticated'));
				break;

			// we redirect back to your login page because something went wrong
			default:
				return Redirect::to(Config::get('sentrysocial::sentrysocial.url.login'));
				break;
		}
	}

	public function get_register()
	{
		return View::make('sentrysocial::register');
	}

	public function post_register()
	{
		$social = Session::get('sentrysocial');

		$social['user'] = array_merge($social['user'], Input::get());

		// do validation
		$rules = array(
			'email'         => 'required|email',
			'email_confirm' => 'required|email|same:email'
		);

		$validation = Validator::make(Input::get(), $rules);

		if ($validation->fails())
		{
			return Redirect::to(Config::get('sentrysocial::sentrysocial.url.register'))->with_input()->with_errors($validation);
		}

		// remove email confirmation
		unset($social['user']['email_confirm']);

		SentrySocial::create($social);

		return Redirect::to('');
	}

	public function post_login()
	{
		$social = Session::get('sentrysocial');

		$social['user'] = array_merge($social['user'], Input::get());

		try
		{
			if (Sentry::login(Input::get('email'), Input::get('password')))
			{
				SentrySocial::create($social, false);

				return Redirect::to(Config::get('sentrysocial::sentrysocial.url.authenticated'));
			}
			else
		    {
		        return Redirect::to(Config::get('sentrysocial::sentrysocial.url.register'))->with('login_error', 'Invalid user name or password.');
		    }
		}
		catch (Sentry\SentryException $e)
		{
		    // issue logging in via Sentry - lets catch the sentry error thrown
		    // store/set and display caught exceptions such as a suspended user with limit attempts feature.
		   	return Redirect::to(Config::get('sentrysocial::sentrysocial.url.register'))->with('login_error', $e->getMessage());
		}


	}
}
