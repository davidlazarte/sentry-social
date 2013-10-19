<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialRemoveUserIdConstraint extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('social', function($table)
        {
            $table->dropUnique('social_user_id_service_unique');
            $table->unique(array('user_id', 'provider'));
        });

        DB::statement('ALTER TABLE social MODIFY user_id INT(10) UNSIGNED NULL');

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('CREATE UNIQUE INDEX social_user_id_service_unique ON social(user_id)');

	}

}