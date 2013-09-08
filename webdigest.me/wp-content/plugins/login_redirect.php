<?php
/*
Plugin Name: David's Custom Solutions
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: Will set the url to redirect to upon login.
Author: David Broadlick
Version: 1.6
Author URI: http://ma.tt/
*/

function my_login_redirect( $redirect_to, $request, $user ){
    //is there a user to check?
    global $user;
    if( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if( in_array( "administrator", $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url();
        }
    }
    else {
        return $redirect_to;
    }
}
add_filter("login_redirect", "my_login_redirect", 10, 3);

add_filter( 'show_admin_bar', '__return_false' );
?>
