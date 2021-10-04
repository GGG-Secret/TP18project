<?php

/**

 * The base configuration for WordPress

 *

 * The wp-config.php creation script uses this file during the installation.

 * You don't have to use the web site, you can copy this file to "wp-config.php"

 * and fill in the values.

 *

 * This file contains the following configurations:

 *

 * * MySQL settings

 * * Secret keys

 * * Database table prefix

 * * ABSPATH

 *

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */


// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', 'bitnami_wordpress' );


/** MySQL database username */

define( 'DB_USER', 'bn_wordpress' );


/** MySQL database password */

define( 'DB_PASSWORD', 'c82c70a9c4019c7e37fb83e1a88db09a496302b9b59df9ed4a9651e5658f03bf' );


/** MySQL hostname */

define( 'DB_HOST', 'localhost:3306' );


/** Database charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8' );


/** The database collate type. Don't change this if in doubt. */

define( 'DB_COLLATE', '' );


/**#@+

 * Authentication unique keys and salts.

 *

 * Change these to different unique phrases! You can generate these using

 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.

 *

 * You can change these at any point in time to invalidate all existing cookies.

 * This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define( 'AUTH_KEY',         'F!*<v<4Ohk^sRpsT)u(:LA&eHcR(I<gR|=7^]ms@:zLY3$hoON%hK_liH-J{c2/K' );

define( 'SECURE_AUTH_KEY',  '`K4R<M;*s~!/=>euEY)rY!b/T@JtUc-7d@:|~9=ki|rB|j5=y5lB,8[l-73C~9!m' );

define( 'LOGGED_IN_KEY',    '-91C`{*UNh*_:tb:o*bMsZtlJ$GeWe$m350GZMyU)TU#Y/rR_@{D1P1 a7VjZ&Ib' );

define( 'NONCE_KEY',        '{l{{eXr!oxoRR/rCk)TNs4=@K7I]cHAM9_:5|[sJ`MMfnd7k,aFGA%3.rp%}?c7v' );

define( 'AUTH_SALT',        '%lKgN(nBdu<cTFB9K*F|_gP)dIANE|Q&:3x8y+KL[gHxbUSZgfdont2[7vJ![*MR' );

define( 'SECURE_AUTH_SALT', 'eP%hYP:t5@#c(t]_~%~qVx7Gdf#!=JdzH,#d:9N%tB216YWHp~FhQdN6[<mzsRMJ' );

define( 'LOGGED_IN_SALT',   '%nWANu%psYf2wPW+i6maE`K!joDtoH[DT)imAVE0]S94?yQ[~i.O).xJ E7]v~/X' );

define( 'NONCE_SALT',       'Bxt@Ptl=Gd-g5]~:>6dFBFL3`{7.VP.@{UPovBmDg?AA[wQv-=2B&P=,`o?P]~TO' );


/**#@-*/


/**

 * WordPress database table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'wp_';


/**

 * For developers: WordPress debugging mode.

 *

 * Change this to true to enable the display of notices during development.

 * It is strongly recommended that plugin and theme developers use WP_DEBUG

 * in their development environments.

 *

 * For information on other constants that can be used for debugging,

 * visit the documentation.

 *

 * @link https://wordpress.org/support/article/debugging-in-wordpress/

 */

define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */




define( 'FS_METHOD', 'direct' );
/**
 * The WP_SITEURL and WP_HOME options are configured to access from any hostname or IP address.
 * If you want to access only from an specific domain, you can modify them. For example:
 *  define('WP_HOME','http://example.com');
 *  define('WP_SITEURL','http://example.com');
 *
 */
if ( defined( 'WP_CLI' ) ) {
	$_SERVER['HTTP_HOST'] = '127.0.0.1';
}

define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] . '/' );
define( 'WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';

/**
 * Disable pingback.ping xmlrpc method to prevent WordPress from participating in DDoS attacks
 * More info at: https://docs.bitnami.com/general/apps/wordpress/troubleshooting/xmlrpc-and-pingback/
 */
if ( !defined( 'WP_CLI' ) ) {
	// remove x-pingback HTTP header
	add_filter("wp_headers", function($headers) {
		unset($headers["X-Pingback"]);
		return $headers;
	});
	// disable pingbacks
	add_filter( "xmlrpc_methods", function( $methods ) {
		unset( $methods["pingback.ping"] );
		return $methods;
	});
}
