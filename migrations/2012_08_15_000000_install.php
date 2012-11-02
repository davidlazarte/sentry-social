<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst Software Licence.
 *
 * @package    Sentry Social
 * @version    1.1
 * @author     Cartalyst LLC
 * @license    Cartalyst Software Licence - http://cartalyst.com/licence
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
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
