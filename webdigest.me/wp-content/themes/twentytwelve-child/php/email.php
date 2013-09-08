<?php

require_once('../config.php');
require_once('libraries/recaptcha/recaptchalib.php');
require_once('MoodleDatabase.php');
$publickey = "6LfKdeASAAAAAIWOGkhFnJrKkzqSfPTfhVFI3vsN";
$privatekey = "6LfKdeASAAAAANF-78oZRc6o3Z6aMmoiKOnVm6jp";

require_login(); //requires that the user be logined in to view this page.

global $USER;
global $DB;
//echo "email.php";


$response = recaptcha_check_answer($privatekey,
$_SERVER["REMOTE_ADDR"],
$_POST["recaptcha_challenge_field"],
$_POST["recaptcha_response_field"]);
 
if ($response->is_valid && isset($_POST["recaptcha_challenge_field"]) && isset($_POST["recaptcha_response_field"]))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $subject_type = "TAASA elearning: ";
    //$uid = $USER->id
    //$username = $DB->get_field('user', 'username', 'id', $uid);
    $Database = new MoodleDatabase();
    $username = $Database->get_username($USER->id);

    $name .= " ($username), \n\n" . $message;

    $message = $name;
//elearning@taasa.org
    $result = array();
    
    if(isset($email) && isset($name) && isset($subject) && isset($message))
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
                if(mail("elearning@taasa.org", $subject_type . $subject . ' ', $message))
                {
                    $result['success'] = true;
                    $result['message'] = "Your message was sent.";
                }
                else
                {
                    $result['success'] = false;
                    $result['message'] = "Your message was not successfully sent.";
                }
        }
        else
        {
            $result['success'] = false;
            $result['message'] = "Your message was not successfully sent.";
        }
    }
    else
    {
        $result['success'] = false;
        $result['message'] = "Your message was not successfully sent.";
    }
    echo json_encode($result);
}
else
{
    $result['success'] = false;
    $result['message'] = "Please retry reCAPTCHA.";
    echo json_encode($result);
}



?>