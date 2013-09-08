<?php
require_once('Database.php');
require_once('/home/dbroadli/public_html/webdigest.me/wp-load.php');

$result = array();

if(is_user_logged_in() && isset($_POST['link']) && isset($_POST['name'])) /*FIX*/
{
    $link = $_POST['link'];
    $name = $_POST['name'];
    $uid = get_current_user_id();

    $Database = new Database();
    $username = $Database->post_cherry($uid, $link, $name);

    $result['success'] = true;
    $result['message'] = "Posted Successfully.";
    echo json_encode($result);
}
else
{
    $result['success'] = false;
    $result['message'] = "Link not set OR Name not set OR User not logged in";
    echo json_encode($result);
}

?>