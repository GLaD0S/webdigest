<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'dbroadli_webdigest');

/** MySQL database username */
define('DB_USER', 'dbroadli_s1m0ne');

/** MySQL database password */
define('DB_PASSWORD', 'xMbCFG5a9l&u');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Wr!+|->K~KoD0}9p,|/?H*j=1AO+iOvUoBHMXo{JV6|F,)m1w0T=Wpm<XtkY,)YU');
define('SECURE_AUTH_KEY',  'K)S0FItF-d<*89%{)[ZqCALwYTf]/vD-Xb|^)%(Yu9;NAdg3xl]zhdx=W3`6&J+7');
define('LOGGED_IN_KEY',    '=5kXfB CD}j0iAv%PK*lOXL`Hcufj|oC+9%0b]3P:B@YvNV4=d$WD<yuOb1GcGm&');
define('NONCE_KEY',        'k}`xq[iC-v]||P6,Rb)$.tgW)0qZ+),5R`M91r@0a9nG-NVOjCN2-2!PV3FMc+^X');
define('AUTH_SALT',        'Bea}YHU$|ZX -Zcv,e:[1{e~gMb4=vqPLPVLyQZ?zd0. ;a(n~G~qtD[@a17#5*`');
define('SECURE_AUTH_SALT', 'lE--f`ktB21#0BL:|1M9LH/b5:J5/tS|aDvyHeAh+53:[=|I+ibt-b?<}f~[IT]C');
define('LOGGED_IN_SALT',   'M_ofkigb ?Jw_K6ola3|!@F2GdE(;H`yIhj@G[>3qU_iLlIa2#f/varey~kx cs]');
define('NONCE_SALT',       'SI{m^N2-Mk6_OwG8@gB6H;<y(3uIsEAqvV/Y<?<X#LL4|o;`||*4[_;3nQX72McI');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wd_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
