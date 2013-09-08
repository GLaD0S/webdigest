<?php
/*
Template Name: Followers
*/
require_once('wp-content/themes/twentytwelve-child/php/Database.php');
$Database = new Database();

$uid = 0;
$username = 0;

$result = 0;

$render = "";
$options = "";
$render_sucess = false;
$admin = false;
$is_logged = is_user_logged_in();
$logged_user = get_current_user_id();
$is_following = false;


if(isset($_GET["uid"]))
{
	$uid = $_GET["uid"];
	if($Database->verify_uid($uid))
	{
		$result = $Database->get_followers_id($uid);
		
		if($uid == get_current_user_id())
			$admin = true;
		else
			die(0);
	}
}
else if(isset($_GET["username"]))
{
	$username = $_GET["username"];
	if($Database->verify_username($username))
	{
		$result = $Database->get_followers_username($username);
		if($Database->uid_from_username($username) == get_current_user_id())
			$admin = true;
		else
			die(0);
	}
}

if($result)
{
	$i = 0;
	while($i < count($result))
	{
		$user = $result[$i]['user'];
		$following = $result[$i]['following'];
		$user_login = $result[$i]['user_login'];
		$render .= "<div class='follower'>";
		$render .= "<p class='link-to-user'><a href='http://www.webdigest.me/?page_id=4&uid=$following'>$user_login</a></p>";
		$render .= "</div>";
		$i++;
	}

	$render_sucess = true;
}

get_header();
?>
	<div id="left-options">&nbsp;</div>
	<div id="link-blog">
	<div id='user-title'>
	<?php 
	$username = "";
	if(isset($_GET["uid"]))
		$username = $Database->username_from_uid($_GET["uid"]);
	if(isset($_GET["username"]))
		$username = $_GET["username"];
	echo $username;
	?></div>
	<div id="admin-tools">
	</div>
	<div id="link-posts">
		<?php
			if($render_sucess && $admin)
			{
				echo $render;
			}
		?>
	</div>

	</div>
<?php
get_footer();
?>


