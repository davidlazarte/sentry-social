<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentrySocialRedoProviderIndex extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social', function($table)
        {
            $table->dropUnique('social_service_uid_unique');
            $table->unique(array('provider', 'uid'));
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
            $table->dropUnique('social_provider_uid_unique');
        });

        DB::statement('CREATE UNIQUE INDEX social_service_uid_unique ON social(uid)');

    }

}