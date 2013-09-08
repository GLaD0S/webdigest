<?php
require_once("php/Database.php");

$Database = new Database();

/*$user = 1;
$follow = 2;
$Database->follow(1,2);*/
$uid = 1;
$num = 5;
$last = 0;

$result = $Database->get_cherries_id($uid, $num, $last);
$fieldlist = array("url", "name", "time");

$i = 0;
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

/*
$is_following = $Database->is_following(1,2);

if($is_following == true)
	echo "<p>is following is true</p>";
else
	echo "<p>is following is false</p>";

$is_following = $Database->is_following(1,3);

if($is_following == true)
	echo "<p>is following is true</p>";
else
	echo "<p>is following is false</p>";*/
?>