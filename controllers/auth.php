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

class SentrySocial_Auth_Controller extends Controller
{
	public $restful = true;

	public function get_session($provider)
	{
		return SentrySocial::forge($provider)->authenticate();
	}

	public function get_callback($provider)
	{
		$status = SentrySocial::forge($provider)->login();

		switch ($status)
		{
			case 'register':
				return Redirect::to('sentrysocial/auth/register');
				break;

			case 'authenticated':
				return Redirect::to('');
				break;

			default:
				return Redirect::to('login');
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
			return Redirect::to('sentrysocial/auth/register')->with_input()->with_errors($validation);
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

				return Redirect::to('');
			}
			else
		    {
		        return Redirect::to('sentrysocial/auth/register')->with('login_error', 'Invalid user name or password.');
		    }
		}
		catch (Sentry\SentryException $e)
		{
		    // issue logging in via Sentry - lets catch the sentry error thrown
		    // store/set and display caught exceptions such as a suspended user with limit attempts feature.
		   	return Redirect::to('sentrysocial/auth/register')->with('login_error', $e->getMessage());
		}


	}
}