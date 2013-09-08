<?php

require_once('config.php');

class Database
{
	private $isconnected = false;
	private $dbhost;
	private $dbuser;
	private $dbpass;
	private $dbname;
	private $connect;
	private $cur_field_list = array();

	/*grab the database information neccecary.*/
	function __construct()
	{
		global $dbhost, $dbuser, $dbpass, $dbname;
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
	}

	function get_posts()
	{
		$this->connect();

		$query = "SELECT post_author, post_date FROM wd_posts";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("post_author", "post_date");
		$this->cur_field_list = $fieldlist;
		$formatted_results = 0;
		$formatted_results = $this->format_result($result, $fieldlist);
		//var_dump($formatted_results);
		
		if($formatted_results)
			return $formatted_results;
		else
			return null;
	}

	function get_cherries_id($uid, $num, $last)
	{
		$this->connect();

		$plus = $num;
		$plus = $this->connect->real_escape_string($plus);
		$last = $this->connect->real_escape_string($last);

		$query = "SELECT cherries.url, cherries.name, cherries.time FROM cherries LEFT JOIN wd_users ON cherries.uid = wd_users.ID WHERE wd_users.ID ='$uid' ORDER BY time DESC LIMIT $last, $plus";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("url", "name", "time");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results)
			return $formatted_results;
		else
			return null;
	}

	function get_cherries_username($username, $num, $last)
	{
		$uid = $this->uid_from_username($username);
		return $this->get_cherries_id($uid, $num, $last);
	}

	function post_cherry($uid, $link, $name)
	{
		$this->connect();
		$query = "INSERT INTO cherries (uid, url, name) VALUES ('$uid', '$link', '$name')";
		$result = mysqli_query($this->connect, $query);
		echo $this->connect->error;
		$this->disconnect();
	}

	function follow($user, $following)
	{
		echo "in method";
		$is_following = false;
		if($this->is_following($user, $following))
			$is_following = true;
		else
			$is_following = false;

		if($is_following == false)
		{
			echo "oh hey";
			$this->connect();
			$query = "INSERT INTO follow (user, following) VALUES ('$user', '$following')";
			$result = mysqli_query($this->connect, $query);
			echo $this->connect->error;
			$this->disconnect();

			return true;
		}
		else
			return false;

	}

	function unfollow($user, $following)
	{
		$is_following = false;
		if($this->is_following($user, $following))
			$is_following = true;
		else
			$is_following = false;

		if($is_following == true)
		{
			$this->connect();
			$query = "DELETE FROM follow WHERE user='$user' AND following='$following'";
			$result = mysqli_query($this->connect, $query);
			echo $this->connect->error;
			$this->disconnect();
			
			return true;
		}
		else
			return false;
	}

	function is_following($user, $following)
	{
		$this->connect();
		$query = "SELECT user FROM follow WHERE user='$user' AND following='$following'";
		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;
		$this->disconnect();

		$fieldlist = array("user");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);

		if($formatted_results[0]["user"])
			return true;
		else
			return false;
	}

	function get_followers_id($uid)
	{
		$this->connect();

		$query = "SELECT follow.user, follow.following, wd_users.user_login FROM follow LEFT JOIN wd_users ON follow.following = wd_users.ID WHERE follow.user ='$uid'";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("user", "following", "user_login");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results)
			return $formatted_results;
		else
			return null;
	}

	function get_followers_username($username)
	{
		$uid = $this->uid_from_username($username);
		return $this->get_followers_id($uid);
	}

	function verify_uid($uid)
	{
		$this->connect();

		$query = "SELECT ID FROM wd_users WHERE ID = '$uid'";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("ID");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results[0]["ID"])
			return true;
		else
			return false;
	}

	function verify_username($username)
	{
		$this->connect();

		$query = "SELECT user_login FROM wd_users WHERE user_login = '$username'";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("user_login");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results[0]["user_login"])
			return true;
		else
			return false;

	}

	function uid_from_username($username)
	{
		$this->connect();

		$query = "SELECT ID FROM wd_users WHERE user_login = '$username'";

		$result = mysqli_query($this->connect, $query);

		//echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("ID");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results[0]["ID"])
			return $formatted_results[0]["ID"];
		else
			return false;
	}

	function username_from_uid($uid)
	{
		$this->connect();

		$query = "SELECT user_login FROM wd_users WHERE ID = '$uid'";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;
		$this->disconnect();

		$fieldlist = array("user_login");
		$this->cur_field_list = $fieldlist;
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results[0]["user_login"])
			return $formatted_results[0]["user_login"];
		else
			return false;
	}

	function format_result($result, $fieldlist)  
	{
		$fields = count($fieldlist);
		$results_array = array();
		$i = 0; 					//mysqli row loop
		$j = 0;						//for fields loop

		while ($row = mysqli_fetch_assoc($result))
		{
			while($j < $fields)
			{
				$fieldname = $fieldlist[$j];
				$results_array[$i][$fieldname] = $row[$fieldname];
				$j++;
			}
			$j = 0;
			$i++;
		}

		return $results_array;
	}


	function print_formatted_results($formatted_results, $fields, $name)
    {
    	$num = count($formatted_results);
    	$i = 0;
    	$output ="";
    	$output .= "<div id='formatted_results' style='background-color:white; position:relative;'>";
    	$output .= "<p style='font-size:130%; font-weight:bold;'>$name</p>";
    	$output .= "<p style='font-weight:bold;'>array($num){</p>";
    	while($i < $num)
    	{
    		$numinner = count($fields);
    		$output .= "<p style='font-weight:bold;'>&nbsp;&nbsp;array($numinner){</p>";
    		$j = 0;
    		while($j < $numinner)
    		{
    
    			$field = $fields[$j];
    			$value = $formatted_results[$i][$fields[$j]];
    			$output .= "<p>";
    			$output .= "&nbsp;&nbsp;&nbsp;&nbsp;$j: $field, $value";
    			$output .= "</p>";
    			$j++;
    		}
    		
    		$output .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;}</p>";
    		$i++;
    	}
   		$output .= "<p>}</p>";
    	$output .= "</div>";
    
    	return $output;
    
    }

    function get_cur_field_list()
    {
    	return $this->cur_field_list;
    }

	function connect()
	{
		$this->connect = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
		if(!mysqli_connect_errno($this->connect))
			$this->isconnected = true;
		else
			exit();
	}

	function disconnect()
	{
		mysqli_close($this->connect);
		$this->isconnected = false;
		$this->connect = 0;
	}

}
?>
