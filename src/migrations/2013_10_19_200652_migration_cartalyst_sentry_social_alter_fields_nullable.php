<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialAlterFieldsNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE social MODIFY oauth2_access_token varchar(255) NULL');
        DB::statement('ALTER TABLE social MODIFY oauth2_refresh_token varchar(255) NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE social MODIFY oauth2_access_token varchar(255) NOT NULL');
        DB::statement('ALTER TABLE social MODIFY oauth2_refresh_token varchar(255) NOT NULL');
	}

}