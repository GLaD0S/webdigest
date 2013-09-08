<?php
/*
Template Name: Link-Blog
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
		$result = $Database->get_cherries_id($uid, 5, 0);
		
		if($uid == get_current_user_id())
			$admin = true;
	}
}
else if(isset($_GET["username"]))
{
	$username = $_GET["username"];
	if($Database->verify_username($username))
	{
		$uid = $Database->uid_from_username($username);
		$result = $Database->get_cherries_username($username, 5, 0);
		if($Database->uid_from_username($username) == get_current_user_id())
			$admin = true;
	}
}
/*
if($result)
{
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


	$render_sucess = true;
}*/

if(!$admin && $is_logged)
{
	$options = "<div id='user-options'>";
	$is_following = $Database->is_following($logged_user, $uid);
	if($is_following)
	{
		$options .= "<div id='link-follow'>unfollow</div>";
	}
	else
		$options .= "<div id='link-follow'>follow</div>";
	$options .= "</div>";
}

get_header();
?>
	<div id="left-options">
	&nbsp;
	<?php 
		echo $options;
	?>
		<div id="admin-tools">
		<?php
			if($admin)
			{	?>
				<div id='linkform-section'>
				<div id='linkform-title'>Post A Link</div>
	 			<form id='linkform' method='post'>
  	 			<span class='linkform-element-title'>Link</span><br><input class='linkform-input' type='text' name='link' size='20'><div class='linkform-response' id='linkform-link-response'>&nbsp;</div>
  	 			<span class='linkform-element-title'>Name</span><br><input class='linkform-input' type='text' name='name' size='20'><div class='linkform-response' id='linkform-name-response'>&nbsp;</div>
	 			<!--<input class='linkform-submit' type='submit' value='Send'>-->
	 			</form>
	 			<div id='linkform-submit'>Submit</div>
	 			<div id='server_response'>&nbsp;</div>
	 			</div>
	 			<?php
			}
		?>
		</div>
	</div>
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
	<div id="link-posts">
		<div id="main-content">
		<?php
			if($render_sucess)
			{

				echo $render;
				

			}
		?>
		</div>
		<div id='load-more'>Load More</div>
	</div>
	</div>

	<script>

	<?php
	echo "var uid_of_page = $uid;";
	?>

	$(document).ready(function() {
  		load_first_links();
	});

	$('#linkform-submit').on('click', function(event) {
	//alert("Handler for .click() called.");
	valid = ValidateForm();
    if(valid)
    {
	var link = $('input[name=link]').val();
	var name = $('input[name=name]').val();
	var sending = $.ajax({
			type: 'POST',
			url: 'wp-content/themes/twentytwelve-child/php/post.php',
			data: {link: link, name: name},
			success: function(data)
					 {
					 	response = $.parseJSON(data);
					 		
					 	if(response.success == true)
					 	{
					 		$('input[name=link]').val('');
					 		$('input[name=name]').val('');
					 	}
					 	$('#server_response').text(response.message);

					 	$('#main-content').html('');
					 	load_first_links();
					 },
			error: function(){
	         	alert('something went wrong');
	       }
		});
	}
	});

	$('#link-follow').on('click', function(event) {
	//alert("Handler for .click() called.");

	var sending = $.ajax({
			type: 'POST',
			url: 'wp-content/themes/twentytwelve-child/php/follow.php',
			data: {uid_of_page: uid_of_page },
			success: function(data)
					 {
					 	var follow = $("#link-follow").html();
					 	if(follow == "follow")
					 		$("#link-follow").html("unfollow");
					 	else
					 		$("#link-follow").html("follow");

					 },
			error: function(){
	         	alert('something went wrong');
	       }
		});
	});

	function load_first_links()
	{
		$("<div>").load("wp-content/themes/twentytwelve-child/php/load_links.php?uid=" + uid_of_page + "&links=0", function() {
			$("#main-content").append($(this).html());
		});
	}

	$("#load-more").click(function(){
		var number_of_links = $(".link-post").length;
		$("<div>").load("wp-content/themes/twentytwelve-child/php/load_links.php?uid=" + uid_of_page + "&links=" + number_of_links, function() {
			$("#main-content").append($(this).html());
		});
	});



	/*$('textarea[name=message]').blur(function() {
  		var value = $('textarea[name=message]').val();
  		var validate = ValidateMessage(value, '#form_response_message');
	});*/

	function ValidateForm()
	{
		var link = $('input[name=link]').val();
		var name = $('input[name=name]').val();
		var valid;
		valid = ValidateLink(link, "#linkform-link-response");
		valid = ValidateName(name, "#linkform-name-response");
		return valid;
	}

	function ValidateLink(value, response)
	{
		if(value)
		{
			$(response).html('&nbsp;');
			return true;
		}
		else
		{
			$(response).text('The URL you entered is not valid.');
			return false;
		}
	}

	function ValidateName(value, response)
	{
		if(value)
		{
			$(response).html('&nbsp;');
			return true;
		}
		else
		{
			$(response).text('You must name your post.');
			return false;
		}
	}

	</script>

<?php
get_footer();
?>


