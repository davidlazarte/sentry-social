### Configuring Sentry Social

Configuring Sentry Social is remarkably easy and varies, depending on how you would like to use it.


#### Laravel 4

To configure Sentry Social in Laravel 4, you simply need to:

1. Run `php artisan config:publish cartalyst/sentry-social`. This will publish your configuration to `app/config/packages/cartalyst/sentry-social`.
2. Open up the `services.php` configuration. In here, you'll see a number of `connections`. These are the available connections you have with Sentry Social.
3. Run `php artisan migrate --package=cartalyst/sentry-social` so that your database has the latest schema chagnes applied.

For each connection you wish to use, you will need to add your application's `key` and `secret`. You can also add your own connections.

#### Everywhere Else

> **Note:** We are working towards easier integration with other frameworks in the future with Sentry Social 2, namely FuelPHP and CodeIgniter.

Once you have installed and initialized an instance of `Cartalyst\SentrySocial\Manager`, simple call:

	$manager->register('name', array(
		'key' => 'application_key_here',
		'secret' => 'application_secret_here',
	));
