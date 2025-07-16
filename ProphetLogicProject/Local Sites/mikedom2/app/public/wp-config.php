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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          '7ye?1UJ/+f}d}~2^GP(^)+=eU/=>nf;1a6^KAXk$S%+.Hd1ir0x|ASq&#$aEA&}:' );
define( 'SECURE_AUTH_KEY',   '5xEy=4b`{HDl]KjyKr1&B|@WMHFcyqQVpe0NhULk$;*+=}JQ=okuUT.-I2[is!FW' );
define( 'LOGGED_IN_KEY',     '+x_@J3*aa$hM+Rx9s()jkv+8UzV8Mpzf+Ll{MD vYQQZy8<F=1%zhkF1sY=O.oBL' );
define( 'NONCE_KEY',         'yZluwV^V9,RJqH`-(XM=t|seG:ntK_ab5>a#vLeeO^:5)M|c &}n57(RNvd`Vzw/' );
define( 'AUTH_SALT',         'D/:_.@A pZe`jV$2LfJX+C:GmcB^SlC`!jK$fa0;l8}:Ca?/R9LUeIhx!_1 _UAh' );
define( 'SECURE_AUTH_SALT',  'PI@!X-q?rJaV@+}bsq=)YM$+KsTL&{xqC)}b-,}K}DNlEzTHX4K4~`t6@QQ bkB!' );
define( 'LOGGED_IN_SALT',    'q-V0XCKvR%D-/n4GqH?X-`eARSw+Zq[xA/+LtvUDG@8M8+ >K&.hnk;8#nIel;G4' );
define( 'NONCE_SALT',        'j1c>2=WBu^]*E9n+.]Ats8OB55sMBfE(uA.qJ&YW^10J&@s<sV&)l)mcYZ:)ThFB' );
define( 'WP_CACHE_KEY_SALT', '][W8#hMJ]5][Jrkb?zzdbs7YmFr7g+A,eRMV1s-I/Qe_naM-kVQOY9wjFOGh0eC<' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
	define( 'WP_DEBUG', true );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );

define( 'SCRIPT_DEBUG', true );

define( 'WP_DEBUG_LOG', 'C:\\Users\\themy\\Local Sites\\mikedom2\\app\\public/wp-content/uploads/debug-log-manager/mikedom2local_20240623174647198593_debug.log' );

define( 'WP_DEBUG_DISPLAY', false );

define( 'DISALLOW_FILE_EDIT', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
