<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="wp-content/themes/twentytwelve-child/scripts/custom.js"></script> <!-- custom -->
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<header id="masthead" class="site-header" role="banner">
		<hgroup>
			<?php $header_image = get_header_image();
			if ( ! empty( $header_image ) ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
			<?php endif; ?>
		</hgroup>

		<?php 
		if(is_front_page())
		{ ?>
			<?php
				if(is_user_logged_in())
					echo "<div id='logo-as-text' style='border-bottom: 1px solid #ededed;'>";
				else
					echo "<div id='logo-as-text'>";
			?>

			<div id='logo-text'><p>Web Digest</p></div>
			<div id='logo-under-text'><p>Meaningful social discovery through friends</p></div>
			</div>
				
		<?php } ?>

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<h3 class="menu-toggle"><?php _e( 'Menu', 'twentytwelve' ); ?></h3>
			<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentytwelve' ); ?>"><?php _e( 'Skip to content', 'twentytwelve' ); ?></a>
			<?php
				//wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) );
				if(is_user_logged_in())
					wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary', 'menu_class' => 'nav-menu' ) );
				else
					wp_nav_menu( array( 'theme_location' => 'not_logged_in', 'menu_id' => 'not_logged_in', 'menu_class' => 'nav-menu' ) );
			
				if(is_user_logged_in())
				{
					$uid = get_current_user_id();
					echo "<a href='http://www.webdigest.me/?page_id=4&uid=$uid'>Your Web Digest</a>&nbsp;&nbsp;&nbsp;<a href='http://www.webdigest.me/?page_id=11&uid=$uid'>Following</a>";
					if(is_front_page())
					{
						//$logout_html = "&nbsp;&nbsp;&nbsp<a href=". wp_logout_url("http://www.webdigest.me/"); . ">Logout</a><br>";
						echo "&nbsp;&nbsp;&nbsp<a href='http://www.webdigest.me/wp-admin/profile.php#password'>Change Password</a>";
						echo "&nbsp;&nbsp;&nbsp<a href=". wp_logout_url("http://www.webdigest.me/") . ">Logout</a>";
					}
				}

				if(!is_front_page() && is_user_logged_in())
					echo "&nbsp;&nbsp;&nbsp<a href='http://www.webdigest.me/'>Home</a><br>";
			?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="main" class="wrapper">