<?php
session_start();
$load_amount = 10;

if(isset($_GET['uid']))
{
	$uid = $_GET['uid'];
	//echo "uid was set: $uid";
}
else
{
	die(0);
}

if(isset($_GET['links']))
{
	$links = $_GET['links'];
	//echo "links was set: $links";
}
else
{
	die(0);
}

require_once('Database.php');

$Database = new Database();

$last = $links;
$num = $load_amount;

$result = $Database->get_cherries_id($uid, $num, $last);
$fieldlist = array("url", "name", "time");

$i = 0;
$render = "";
$count = count($result);
//echo "<p>$count</p>";
while($i < count($result))
{
	$url = $result[$i]['url'];
	$name = $result[$i]['name'];
	$time = $result[$i]['time'];
	$render .= "<div class='link-post'>";
	$render .= "<p class='link'><a href='$url'>$name</a></p>";
	$render .= "<p class='timestamp'>$time</p>";
	$render .= "</div>";
	$i++;
}

echo $render;
//$_SESSION['links'] = $num;


?>