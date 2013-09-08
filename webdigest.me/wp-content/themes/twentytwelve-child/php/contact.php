<?php

require_once('../config.php');
require_once('libraries/recaptcha/recaptchalib.php');
$publickey = "6LfKdeASAAAAAIWOGkhFnJrKkzqSfPTfhVFI3vsN";
$privatekey = "6LfKdeASAAAAANF-78oZRc6o3Z6aMmoiKOnVm6jp";

require_login(); //requires that the user be logined in to view this page.

global $DB; //declaring this imports the database global object for use in this file. 
global $USER; //imports the current user object.

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin'); //set_pagelayout will set this pages layout to any declared in the current theme's config file.
$PAGE->set_title("taasaelearning");
$PAGE->set_heading("Blank page");
$PAGE->set_url($CFG->wwwroot.'/contact.php');


echo $OUTPUT->header();

echo "<div id='contact_page'>
	 <div id='contact_page_header'>Contact Us</div>
	 <form id='contact_form' method='post'>
  	 <span class='contact_page_name'>Name:</span><br><input class='contact_page_input' type='text' name='name' size='30'><div class='contactpage_form_response' id='form_response_name'>&nbsp;</div>
  	 <span class='contact_page_name'>E-Mail Address:</span><br><input class='contact_page_input' type='text' name='email' size='30'><div class='contactpage_form_response' id='form_response_email'>&nbsp;</div>
  	 <span class='contact_page_name'>Subject:</span><br><input class='contact_page_input' type='text' name='subject' size='30'><div class='contactpage_form_response' id='form_response_subject'>&nbsp;</div>
  	 <span class='contact_page_name'>Message:</span><br><textarea class='contact_page_input' name='message' onkeyup='countChar(this)' cols='40' rows='7'></textarea><div class='contactpage_form_response' id='form_response_message'>0 Characters, your message must be at least 20 characters</div>";
echo "<div id='server_response'>&nbsp;</div>";
echo recaptcha_get_html($publickey, 'Please Try Again');
echo "<input class='contact_page_submit' type='submit' value='Send'>
	 </form>
	 </div>

	<script type='text/javascript'>

	$(document).ready(function() {
		$('input[name=name]').val('');
		$('input[name=email]').val('');
		$('input[name=subject]').val('');
		$('textarea[name=message]').val('');
 	});

	$('input[name=name]').blur(function() {
  		var value = $('input[name=name]').val();
  		var validate = ValidateName(value, '#form_response_name');
 		
	});

	$('input[name=email]').blur(function() {
  		var value = $('input[name=email]').val();
  		var validate = ValidateEmail(value, '#form_response_email');
	});

	$('input[name=subject]').blur(function() {
  		var value = $('input[name=subject]').val();
  		var validate = ValidateSubject(value, '#form_response_subject');
	});

	$('textarea[name=message]').blur(function() {
  		var value = $('textarea[name=message]').val();
  		var validate = ValidateMessage(value, '#form_response_message');
	});

	function ValidateName(value, response)
	{
		if(value)
		{
			$(response).html('&nbsp;');
			return true;
		}
		else
		{
			$(response).text('You must write your name.');
			return false;
		}
	}

	function ValidateEmail(value, response)
	{
		if(value)
		{
  			var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
  			valid = regex.test(value);
  			if(valid == false)
  				$(response).text('The email you entered is not valid.');
  			else
  				$(response).html('&nbsp;');
  			return valid;
  		}
  		else
  		{
  			$(response).text('You must enter a email address.');
  			return false;
  		}
	}

	function ValidateSubject(value, response)
	{
		if(value)
		{
			$(response).html('&nbsp;');
			return true;
		}
		else
		{
			$(response).text('You must enter a subject.');
  			return false;
		}

		return ' ';
	}


	function ValidateMessage(value, response)
	{
		if(value)
		{
			if(value.length < 20)
			{
				//$(response).text('Your message must be at least 20 characters long.');
				return false;
			}
			else 
			{
				$(response).html('&nbsp;');
				return true;
			}
		}
		else
		{
			//$(response).text('Your message must be at least 20 characters long.');
			return false;
		}

	}

	function ValidateForm()
	{
		var name = $('input[name=name]').val();
		var email = $('input[name=email]').val();
		var subject = $('input[name=subject]').val();
		var message = $('textarea[name=message]').val();

		var name_valid = ValidateName(name, '#form_response_name');
		var email_valid = ValidateEmail(email, '#form_response_email');
		var subject_valid = ValidateSubject(subject, '#form_response_subject');
		var message_valid = ValidateMessage(message, '#form_response_message');

		//alert('results: ' + 'name: ' + name_valid + ' email: ' + email_valid + ' subject: ' + subject_valid + ' message: ' + message_valid);

		if(ValidateName(name, '#form_response_name') && ValidateEmail(email, '#form_response_email') && ValidateSubject(subject, '#form_response_subject') && ValidateMessage(message, '#form_response_message'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function countChar(val) {
        var len = val.value.length;
        if(len < 20)
        {
        	$('#form_response_message').text(len + ' Characters, your message must be at least 20 characters');
        }
        else if(len > 20 && len < 30)
        {
        	$('#form_response_message').html('&nbsp;');
        }
      }

    $('#contact_form').submit(function(event) {
    	
    	event.preventDefault();

    	valid = ValidateForm();
    	if(valid)
    	{
			var name = $('input[name=name]').val();
			var email = $('input[name=email]').val();
			var subject = $('input[name=subject]').val();
			var message = $('textarea[name=message]').val();
			var recaptcha_challenge_field = $('#recaptcha_challenge_field').val();
			var recaptcha_response_field = $('#recaptcha_response_field').val();

			//var url = 'email.php?name=' + name + '&email=' + email + '& subject=' + subject + '&message=' + message;

			var sending = $.ajax({
  				type: 'POST',
  				url: 'email.php',
  				data: {name: name, email: email, subject: subject, message: message, recaptcha_challenge_field: recaptcha_challenge_field, recaptcha_response_field: recaptcha_response_field},
  				success: function(data)
						 {
						 	response = $.parseJSON(data);
						 	//alert(response.success);
						 	//alert(data);
						 		
						 	if(response.success == true)
						 	{
						 		$('input[name=name]').val('');
						 		$('input[name=email]').val('');
						 		$('input[name=subject]').val('');
						 		$('textarea[name=message]').val('');
						 	}
						 	$('#server_response').text(response.message);
						 	Recaptcha.reload();
						 },
				error:function(){
              		alert('something went wrong');
            }
			});



			/*sending.done(function(data){
				alert('hello');
			});*/


		}

    });
	</script>
	 ";

echo $OUTPUT->footer();
?>