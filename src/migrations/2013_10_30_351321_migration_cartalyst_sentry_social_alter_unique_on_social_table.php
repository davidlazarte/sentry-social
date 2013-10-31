<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialAlterUniqueOnSocialTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social', function($table)
		{
			$table->dropUnique('social_provider_uid_unique');
			$table->unique(array('provider', 'user_id'));

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
			$table->dropUnique('social_provider_user_id_unique');
			$table->unique(array('provider', 'uid'));
		});
	}

}
