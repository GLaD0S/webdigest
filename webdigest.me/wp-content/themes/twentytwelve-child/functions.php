<?php

/*my_login_stylesheet() will allow this theme to point to a custom style sheet for the wordpress login page.*/
function my_login_stylesheet() { ?>
    <link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/css/style-login.css'; ?>" type="text/css" media="all" />
<?php }
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

//register_nav_menus('not_logged_in', __( 'Not Logged In Menu', 'twentytwelve' ));

register_nav_menus( array(
	'not_logged_in' => 'Not Logged In Menu'
) );

//this is a new comment
//this is another comment
//this is a third commnet
//this is a fourth comment
?>
