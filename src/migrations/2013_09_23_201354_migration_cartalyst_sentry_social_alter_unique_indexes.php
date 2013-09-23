<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialAlterUniqueIndexes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social', function($table)
		{
			$table->dropUnique('social_service_access_token_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('CREATE UNIQUE INDEX social_service_access_token_unique ON social (provider)');
	}

}
