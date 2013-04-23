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

class MigrationCartalystSentrySocialInstallSocial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('service');
			$table->string('uid');
			$table->timestamps();

			$table->unique(array('user_id', 'service'));
			$table->unique(array('service', 'uid'));

			// $table->integer('user_id')->unsigned();
			// $table->string('service');

			// // When we first use our storage for
			// // authenticating, we won't have a user
			// // ID
			// $table->string('uid')->nullable();

			// $table->string('access_token');

			// // OAuth 1 specific
			// $table->string('access_token_secret');
			// $table->string('request_token');
			// $table->string('request_token_secret');

			// // Common
			// $table->string('refresh_token');
			// $table->string('end_of_life');
			// $table->string('extra_params');

			// $table->timestamps();

			// // We'll add a bunch of unique indexes to
			// // ensure we don't duplicate any data.
			// $table->unique(array('user_id', 'service'));
			// $table->unique(array('service', 'uid'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social');
	}

}
