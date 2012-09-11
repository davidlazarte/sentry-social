<?php
/**
 * OAuth Authorization Request
 *
 * @package    Kohana/OAuth
 * @category   Request
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 *
 * @modified_by  Cartalyst LLC
 * @copyright    (c) 2012 Cartalyst LLC.
 * @version      1.1
 */

namespace SentrySocial;

class Libraries_OAuth_Request_Authorize extends Libraries_OAuth_Request {

	protected $name = 'request';

	// http://oauth.net/core/1.0/#rfc.section.6.2.1
	protected $required = array(
		'oauth_token' => TRUE,
	);

	public function execute(array $options = NULL)
	{
		return \Redirect::to($this->as_url());
	}

} // End Request_Authorize