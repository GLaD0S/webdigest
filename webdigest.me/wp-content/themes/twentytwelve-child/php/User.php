<?php

require_once("Database.php");

/* From the User class a script can determine about a user
*  if the user exists
*  if the user is the admin of a page
*  if the user is following another user
*  if the user is logged in
*
*/

class User
{

	$Database;
	$exists = false;
	$is_logged = false;

	function __construct($uid)
	{
		$Database = new Database();
		if($Database->verify_uid($uid))
		{
			$this->exists = true;

			if($Database)
		}
		else
		{
			$this->exists = false;
		}
	}

	function is_admin()
	{

	}

	function is_following()
	{

	}

	function is_user_logged_in()
	{

	}

	function get_current_user_id()
	{
		return $get_current_user_id();
	}

}



?>