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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'slutprojekt' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'Q=MPht:r[6`8+mU>[~%^]8_=^)F-c>86BiQ^~Q7hk<SK<V{]S8Li[)U?:iwe;8}*' );
define( 'SECURE_AUTH_KEY',  '{]kKV;Z|u-$OYo45W#x8=_aOAB:[YA_u:g7%H5{q&^_yIvwHL]exS|u}<n2P>1pM' );
define( 'LOGGED_IN_KEY',    '1-y{.ezC-~FRPE/#(Q>1NP4?C<^IG!l*!E3nDtj6gH1y^_j1T!dd_<**HR>uQ7uH' );
define( 'NONCE_KEY',        'EwTv|swj^a}A21@dXqlnB/46Q[dfAM7P5TQ8ZeN(qmzj:(4]z&WBoZf.G(fS(899' );
define( 'AUTH_SALT',        'pgk{U^Ct]t1n9O(D,!KI=+UrB|]Pn`L$[Q+D07W=1IZGtQ|d2Z@gq+(92Q=vCv.o' );
define( 'SECURE_AUTH_SALT', '^_KBNs-bEMQkw.Dk%yXcQ9i<yxNqizT~_Km]:%Ounb,.vQw4+PI1edxH[Cd<TJ,*' );
define( 'LOGGED_IN_SALT',   'RJZeuI||-hyFaj-6f1h9F4->Bd@bh+%lc~R^CRV=$GXHp[8hFB WaV12M=Np MkW' );
define( 'NONCE_SALT',       '6-1c=k5<=%%L8d2]]9+hk3RQ]_Xl7OvF`rl_Zj{Ju@)WAL.3XKNWYdr_LH|D7c`4' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
