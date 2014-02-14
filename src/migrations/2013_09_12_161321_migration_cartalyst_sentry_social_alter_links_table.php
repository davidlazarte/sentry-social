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

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialAlterLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social', function($table)
		{
			// Drop out a constraint where only a user could
			// not have multiple instances of the same,
			// provider with different provider IDs
			$table->dropUnique('social_user_id_service_unique');
			$table->dropUnique('social_service_uid_unique');

			// "Services" are now "providers", so rename the columns
			// and switch out indexes
			$table->string('provider')->default('');
			$table->unique(array('provider', 'user_id'));

			// Add two new columns for our OAuth1 token credentials
			// which are used as the equivilent of the access token
			// in OAuth2. We'll keep it separate to make it easier
			// to determine what is what.
			$table->string('oauth1_token_identifier')->nullable();
			$table->string('oauth1_token_secret')->nullable();

			// Namespace the OAuth2 columns as we have with the new
			// OAuth1 columns above.
			$table->string('oauth2_access_token')->nullable();
			$table->string('oauth2_refresh_token')->nullable();
			$table->timestamp('oauth2_expires')->nullable();
		});

		// Drop out the old columns. We need to wipe these anyway
		// as the "access_token" column is shared between OAuth1
		// and OAuth2 in previous schemas.
		Schema::table('social', function($table)
		{
			$table->dropColumn(array(
				'service',
				'extra_params',
				'request_token',
				'request_token_secret',
				'access_token',
				'refresh_token',
				'end_of_life',
			));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('social', function($table)
		{
			$table->unique(array('user_id', 'service'));
			$table->dropUnique('social_provider_user_id_unique');
			$table->string('service')->default('');
			$table->unique(array('service', 'uid'));
			$table->text('extra_params')->nullable();
			$table->string('request_token')->nullable();
			$table->string('request_token_secret')->nullable();
			$table->integer('end_of_life')->nullable();

			$table->string('access_token')->nullable();
			$table->string('refresh_token')->nullable();
			$table->unique(array('service', 'access_token'));
		});

		Schema::table('social', function($table)
		{
			$table->dropColumn(array(
				'oauth2_access_token',
				'oauth2_refresh_token',
				'provider',
				'oauth1_token_identifier',
				'oauth1_token_secret',
			));
		});
	}

}
