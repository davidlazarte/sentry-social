### Installation

Once you have SentrySocial downloaded, you will want to put all the `sentrysocial`
folder in your bundles folder.

Then add the following to your `application/bundles.php` file

	'sentrysocial' => array(
		'auto'    => true,
		'handles' => 'sentrysocial'
	),

Installing the table for SentrySocial is as simple as running its migration.

	php artisan migrate sentrysocial

>**Note:** If you do not want to use the default controller, you can remove `handles` from bundles.php and copy or extend it elsewhere.  This is just a working example controller.

>**Note:** If you are testing on your localhost, you may need to enable the php setting `php_openssl` and possibly apache's `ssl_module` for OAuth2 in it's current state.
