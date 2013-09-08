<?php
/**
 * Template Name: Link-Blog
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Twenty Twelve consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 */
get_header();

require_once('wp-content/themes/twentytwelve-child/php/Database.php');
$Database = new Database();

$uid = 0;
$username = 0;

$uid_valid = false;
$username_valid = false;
$result = 0;

$render = "";
$render_sucess = false;

if(isset($_GET["uid"]))
{
	$uid = $_GET["uid"];
	if($Database->verify_uid($uid))
	{
		$uid = $_GET["uid"];
		$uid_set = true;
		$result = $Database->get_cherries_id($uid);
	}
}
else if(isset($_GET["username"]))
{
	$username = $_GET["username"];
	if($Database->verify_username($username))
	{
		$username = $_GET["username"];
		$username_set = true;
	}
}

if($result)
{
	$i = 0;
	while($i < count($result))
	{
		$url = $result[$i]['url'];
		$name = $result[$i]['name'];
		$time = $result[$i]['time'];
		$render .= "<div class='link-post'>";
		$render .= "<p><a href='$url'>$name</a></p>";
		$render .= "<p>$time</p>";
		$render .= "</div>";
		$i++;
	}

	$render_sucess = true;
}
?>

	<div id="admin-tools">
	</div>
	<div id="link-posts">
		<?php
			if($render_sucess)
			{
				echo $render;
			}
		?>
	</div>

<?php
get_footer();
?>
<!--
<html>
<head>
	<link rel='stylesheet' href='screen.css' type='text/css' media='screen'>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
	<div id="admin-tools">
	</div>
	<div id="link-posts">
		<?php
			/*if($render_sucess)
			{
				echo $render;
			}*/
		?>
	</div>

</body>
</html>
-->

