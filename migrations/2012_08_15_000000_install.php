<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cartalyst
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2012, Cartalyst LLC
 * @link       http://http://sentry.cartalyst.com/licence.html
 */

/**
 * Installs Sentry Social
 */
class SentrySocial_Install {

	public function up()
	{
		Schema::table('social_authentication', function($table) {
			$table->on(Config::get('sentry::sentry.db_instance'));
			$table->create();
			$table->increments('id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('provider');
			$table->string('uid');
			$table->string('token');
			$table->string('secret');
			$table->integer('expires');
			$table->integer('created_at');
			$table->integer('updated_at');
		});
	}

	public function down()
	{
		Schema::table('social_authentication', function($table) {
			$table->on(Config::get('sentry::sentry.db_instance'));
			$table->drop();
		});
	}
}
