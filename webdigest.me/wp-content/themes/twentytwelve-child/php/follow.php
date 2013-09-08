<?php
require_once('Database.php');
require_once('/home/dbroadli/public_html/webdigest.me/wp-load.php');

$Database = new Database();
$result = array();

if(is_user_logged_in() && isset($_POST['uid_of_page'])) /*FIX*/
{
    $uid = $_POST['uid_of_page'];
    $logged_user = get_current_user_id();
    $is_following = $Database->is_following($logged_user, $uid);

    $result['message'] = "Follow Post Successful.";
    $result['is_following'] = $is_following;
    if($is_following == "true")
    {
        $Database->unfollow($logged_user, $uid);
        $result['message'] = "is following";
    }
    else
    {
        $Database->follow($logged_user, $uid);
        $result['message'] = "is not following";
    }

    $result['success'] = true;
    //$result['message'] = "Follow Post Successful.";
    echo json_encode($result);
}
else
{
    $result['success'] = false;
    $result['message'] = "is_following not set OR logged_user not set OR user not logged in";
    echo json_encode($result);
}

?>