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
			$table->renameColumn('service', 'provider');

			// Because of a mishap with how we managed the V2
			// underlying library
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
		});
	}

}
