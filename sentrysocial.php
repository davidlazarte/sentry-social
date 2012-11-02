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

namespace SentrySocial;

use Config;
use DB;
use Exception;
use Sentry;
use Str;

class SentrySocialException extends Exception {}

/**
 * SentrySocial Auth class
 *
 * @package  SentrySocial
 * @author   Daniel Petrie
 */
class SentrySocial
{
	/**
	 * @var  object  OAuth/OAuth2 provider object
	 */
	protected $provider = null;

	/**
	 * @var  string  what OAuth protocal is used ( OAuth/OAuth2 )
	 */
	protected $driver = null;

	/**
	 * Constructor
	 *
	 * @param  string  social provider (facebook/twitter etc)
	 */
	public function __construct($provider)
	{
		// grab the config and make sure it is set
		$config = Config::get('sentrysocial::sentrysocial.providers.'.$provider);

		if (empty($config))
		{
			throw new SentrySocialException(sprintf('Provider "%s" does not exist in the config.', $provider));
		}

		if (empty($config['app_id']))
		{
			throw new SentrySocialException('Config item: app_id is required.');
		}

		if (empty($config['app_secret']))
		{
			throw new SentrySocialException('Config tiem: app_secret is required.');
		}

		// set driver
		$this->driver = $config['driver'];

		// set provider
		if ($this->driver == 'OAuth')
		{
			$this->provider = Libraries_OAuth_Provider::make($provider);
		}
		elseif ($this->driver == 'OAuth2')
		{
			$this->provider = Libraries_OAuth2_Provider::make($provider, array(
				'id'     => $config['app_id'],
				'secret' => $config['app_secret'],
				'scope'  => (isset($config['scope'])) ? $config['scope'] : null,
			));
		}
		else
		{
			throw new SentrySocialException(sprintf('Unsupported Driver: %s. Must be OAuth or OAuth2 (case sensitive)', $this->provider->name));
		}
	}

	/**
	 * Forge
	 *
	 * @param  string  social provider (facebook/twitter etc)
	 */
	public static function make($provider = null)
	{
		// make sure site is set
		if (empty($provider))
		{
			throw new SentrySocialException('Provider must be set.');
		}

		// get the driver
		$driver = Config::get('sentrysocial::sentrysocial.providers.'.$provider.'.driver');

		// if no driver, throw exception
		if (empty($driver))
		{
			throw new SentrySocialException(sprintf('Provider "%s" has no driver or does not exist.', $provider));
		}

		$class = 'SentrySocial\\Driver_'.$driver;

		return new $class($provider);
	}

	/**
	 * Log the user in - also registers
	 */
	public function login()
	{
		// get token
		try
		{
			$token = $this->callback();
		}
		catch(SentrySocialException $e)
		{
			throw new SentrySocialException($e->getMessage());
		}

		// get user info
		$user_hash = $this->get_user_info($token);

		// query the DB to see if uid/provider combo exists
		$user_social = DB::table('social_authentication')
			->where('uid', '=', $user_hash['uid'])
			->where('provider', '=', $this->provider->name)
			->first();

		// if social user exists - update
		if ($user_social)
		{
			$user_social = get_object_vars($user_social);
			// login to sentry and update tokens
			DB::table('social_authentication')
				->where('uid', '=', $user_hash['uid'])
				->where('provider', '=', $this->provider->name)
				->update(array(
					'token'      => isset($token->access_token) ? $token->access_token : '',
					'secret'     => isset($token->secret) ? $token->secret : '',
					'expires'    => isset($token->expires) ? $token->expires : '',
					'updated_at' => static::sql_timestamp(),
				));

			// force login
			Sentry::force_login((int) $user_social['user_id'], $this->provider->name);

			return 'authenticated';
		}

		// the social user does not exist yet, lets see if they are logged into an account
		// already so we can link them to it
		if (Sentry::check())
		{
			$user_id = Sentry::user()->get('id');

			// user does not exist - store in the DB;
			DB::table('social_authentication')->insert(array(
				'user_id' => $user_id,
				'provider'   => $this->provider->name,
				'uid'        => $user_hash['uid'],
				'token'      => isset($token->access_token) ? $token->access_token : '',
				'secret'     => isset($token->secret) ? $token->secret : '',
				'expires'    => isset($token->expires) ? $token->expires : '',
				'created_at' => static::sql_timestamp(),
				'updated_at' => static::sql_timestamp(),
			));

			return 'authenticated';
		}
		// otherwise lets create or allow them to login to a sentry account
		// if the social site doesn't return an email/username we will ask for one
		else
		{
			try
			{
				$email_or_username = Config::get('sentry::sentry.login_column');

				// set username and email
				$username = (isset($user_hash['username']) and ! empty($user_hash['username'])) ? $user_hash['username'] : false;
				$email    = (isset($user_hash['email'])  and ! empty($user_hash['email'])) ? $user_hash['email'] : false;

				if ($email_or_username == 'username')
				{
					$can_auto_create = ($username and $email) ? true : false;
				}
				else
				{
					$can_auto_create = ($email) ? true : false;
				}

				// if we have the email or username and matches sentry's login column, we'll auto register
				if ($can_auto_create)
				{
					// see if email already exists
					$user = DB::table(Config::get('sentry::sentry.table.users'))
						->where('email', '=', $email)
						->first();

					if ($user)
					{
						$user_id = $user->id;
					}
					else
					{
						// grab name and explode
						$name = explode(' ', $user_hash['name']);

						$user = array(
							'username' => $username,
							'email'    => $email,
							// we'll generate a random password
							'password' => \Str::random(24),
							'metadata' => array(
								'first_name' => (isset($name[0])) ? $name[0] : '',
								'last_name'  => (isset($name[1])) ? $name[1] : ''
							),
						);

						if (empty($user['username']))
						{
							unset($user['username']);
						}

						$user_id = Sentry::user()->create($user);
					}

					// user does not exist - store in the DB;
					DB::table('social_authentication')->insert(array(
						'user_id'    => $user_id,
						'provider'   => $this->provider->name,
						'uid'        => $user_hash['uid'],
						'token'      => isset($token->access_token) ? $token->access_token : '',
						'secret'     => isset($token->secret) ? $token->secret : '',
						'expires'    => isset($token->expires) ? $token->expires : '',
						'created_at' => static::sql_timestamp(),
						'updated_at' => static::sql_timestamp(),
					));

					// force login
					Sentry::force_login((int) $user_id, $this->provider->name);

					return 'authenticated';
				}
				else
				{
					// grab name and explode
					$name = explode(' ', $user_hash['name']);

					\Session::put('sentrysocial', array(
						'user' => array(
							'username' => $username,
							'email'    => $email,
							'metadata' => array(
								'first_name' => (isset($name[0])) ? $name[0] : '',
								'last_name'  => (isset($name[1])) ? $name[1] : '',
							),
						),
						'auth' => array(
							'provider'   => $this->provider->name,
							'uid'        => $user_hash['uid'],
							'token'      => isset($token->access_token) ? $token->access_token : '',
							'secret'     => isset($token->secret) ? $token->secret : '',
							'expires'    => isset($token->expires) ? $token->expires : '',
						)

					));

					return 'register';
				}

			}
			catch (\SentryUserException $e)
			{
				throw new \SentrySocialException($e->getMessage());
			}
		}

		return false;
	}

	public static function create($social, $new = true)
	{
		// create and login user
		try
		{
			// see if a password was passed, if not autogen one
			if ( ! isset($social['user']['password']) or empty($social['user']['password']))
			{
				$social['user']['password'] = \Str::random(32);
			}

			// remove username if it is set and not used
			if (isset($social['user']['username']) and empty($social['user']['username']))
			{
				unset($social['user']['username']);
			}

			if ($new)
			{
				$user_id = Sentry::user()->create($social['user']);
				Sentry::force_login((int) $user_id, $social['auth']['provider']);
			}
			else
			{
				$user_id = Sentry::user()->get('id');
			}

			// user does not exist - store in the DB;
			DB::table('social_authentication')->insert($social['auth'] + array(
				'user_id' => $user_id,
				'created_at' => static::sql_timestamp(),
				'updated_at' => static::sql_timestamp(),
			));

			// force login
			\Session::forget('sentrysocial');

			return true;
		}
		catch (\SentryUserException $e)
		{
			throw new \SentrySocialException($e->getMessage());
		}
	}

	/**
	 * Returns an SQL timestamp appropriate
	 * for the currect database driver.
	 *
	 * @return   string
	 */
	protected static function sql_timestamp()
	{
		return date(DB::connection()->grammar()->grammar->datetime);
	}

}
