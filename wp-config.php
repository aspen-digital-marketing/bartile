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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */


define( 'DB_NAME', 'bartile_wp_5i41n' );

/** Database username */
define( 'DB_USER', 'bartile_wp_2sox0' );

/** Database password */
define( 'DB_PASSWORD', '~*~zWLryOq406Iz8' );

/** Database hostname */
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
define('AUTH_KEY', '-t_%KH4]Y80%QI#_dKB08-i-GJJ#WaU@aEt@/6a|gBKGmeUFh*FLN#S;73Ux@0zq');
define('SECURE_AUTH_KEY', 'Bt|rl41;-kl2zG2#ZVO[!Vg!35&|#/73-w36&B~724c#/2-EBf@8%~&kfWF8FWQ1');
define('LOGGED_IN_KEY', 'S1/)VW1~0&R6+lB5Z/aRf42eXY4#VD(4N30Z8_4z|Xi15/1HzQBU)51hB5*xQ3_0');
define('NONCE_KEY', 'ET59%(@kD8h;@H[&7S)PhT46/+GMjPUqw0ve))4[!84;y82LtVB/w_|0J&5YanA+');
define('AUTH_SALT', '9/+0274Zih]oSsye05|v#[pjVSzH&(2;g2!6%-sGzZB&-e81Y8J4|8m9|18Lr5BC');
define('SECURE_AUTH_SALT', 'Q4tO8i:L&SY01NW+K#kNPyI5KrG|ST4[9fI_I:fR(2w;687/80~~AKzaFg3uD37y');
define('LOGGED_IN_SALT', ':64ln0N~[yhW9gE3(&[6V%)vF-7kQaf8-i#15W5mxE5L3[djq389L0p%n)MA+_v&');
define('NONCE_SALT', 'U)J7:aPxaSY:[Nbw#70|ob@mD!)jk(t3!SdN23juaUlGf4sFkDCy!v8D!/fi3HGR');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'zV8E6_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';