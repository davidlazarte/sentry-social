## Install & Configure in Laravel 4

> **Note:** To use Cartalyst's Sentry Social package you need to have a valid Cartalyst.com subscription.
Click [here](https://www.cartalyst.com/pricing) to obtain your subscription.

### 1. Composer {#composer}

---

Open your `composer.json` file and add the following lines:

	{
		"repositories": [
			{
				"type": "composer",
				"url": "http://packages.cartalyst.com"
			}
		],
		"require": {
			"cartalyst/sentry-social": "3.0.*"
		}
	}

Run composer update from the command line

	composer update

### 2. Service Provider {#service-provider}

---

Add the following to the list of service providers in `app/config/app.php`.

	'Cartalyst\SentrySocial\SentrySocialServiceProvider',

> **Note:** If you are using our Themes 2 package, you should register Sentry Social **after** the `ThemeServiceProvider`.

### 3. Alias *(optional)* {#alias}

Add the following to the list of aliases in `app/config/app.php`.

	'SentrySocial' => 'Cartalyst\SentrySocial\Facades\Laravel\SentrySocial',

### 4. Migrations {#migrations}

In order to run the migration successfully, you need to have a default database connection setup on your Laravel 4 application, once you have that setup, you can run the following commands:

Run Sentry migrations

	php artisan migrate --package=cartalyst/sentry

Run Sentry Social migrations

	php artisan migrate --package=cartalyst/sentry-social

### 5. Configuration {#configuration}

After installing, you can publish the package's configuration file into your application, by running the following command:

	php artisan config:publish cartalyst/sentry-social

This will publish the config file to `app/config/packages/cartalyst/sentry-social/config.php` where you can modify your OAuth app credentials.
