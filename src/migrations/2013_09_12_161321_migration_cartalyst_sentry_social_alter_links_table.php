<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialAddAccessTokenSecret extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social', function($table)
		{
			// "Services" are now "providers"
			$table->renameColumn('service', 'provider');

			// Remove out the extra params, they're no good to us
			$table->dropColumn('extra_params');

			// Remove the request token junk, it's totally useless!
			$table->dropColumn('request_token');
			$table->dropColumn('request_token_secret');

			// Add two new columns for our OAuth1 token credentials
			// which are used as the equivilent of the access token
			// in OAuth2. We'll keep it separate to make it easier
			// to determine what is what.
			$table->string('oauth1_token_identifier')->nullable();
			$table->string('oauth1_token_secret')->nullable();

			// Drop out the old columns. We need to wipe these anyway
			// as the "access_token" column is shared between OAuth1
			// and OAuth2 in previous schemas.
			$table->dropColumn('access_token');
			$table->dropColumn('refresh_token');
			$table->dropColumn('end_of_life');

			// Namespace the OAuth2 columns as we have with the new
			// OAuth1 columns above.
			$table->string('oauth2_access_token');
			$table->string('oauth2_refresh_token');
			$table->timestamp('oauth2_expires')->nullable();
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
			$table->renameColumn('provider', 'service');
			$table->text('extra_params')->nullable();
			$table->string('request_token')->nullable();
			$table->string('request_token_secret')->nullable();
			$table->dropColumn(array('oauth1_token_identifier', 'oauth1_token_secret'));
			$table->renameColumn('oauth2_access_token', 'access_token');
			$table->renameColumn('oauth2_refresh_token', 'refresh_token');
			$table->integer('end_of_life')->nullable();
		});
	}

}
