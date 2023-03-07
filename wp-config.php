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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'project_listing' );

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
define( 'AUTH_KEY',         'lbU*zkqF<]V2?Zx}Y@h],=liL<os2#1Uzi95G:INun-6U4].SxNA|0}4N$-bG1Qn' );
define( 'SECURE_AUTH_KEY',  'QhDs?Zh3LqM]Y;3U;3ohIUA>UQ6$o//-&3J+-|ex4beBl:t[YN&duj3upX`Vc1B$' );
define( 'LOGGED_IN_KEY',    'bANX_HG:f`kTm}phUfu2qugo5nWW-^gMaLq/]g2#Ga0tM$~EvkHvGEJ7T^K&Y&e ' );
define( 'NONCE_KEY',        ')=ql~/sY ([k4X`I*L,*`Wbj]FM!OzW9&>&UGf6<~#+(L5PIO>BtqLs29@~X> /^' );
define( 'AUTH_SALT',        '5oOJkXoTV:cOocBF1bdYi*7nQ*BqXTc)mW$M=Eo%|Ud%9#9N:lZNMObj5Lx&Amt!' );
define( 'SECURE_AUTH_SALT', 'XK$ac0&7.649;qbDVw#<3a3`jX$W3BlCoPk+^,>XoF.2Mf>f+!~A[VnGU)0!`b(w' );
define( 'LOGGED_IN_SALT',   'kgb2FHG#Bd)}l@[F]t*l.{!C&&nU@F:GYazxq*]rtg0V_/&h+KMc$V9os Ve{5(N' );
define( 'NONCE_SALT',       '(p5i51$9Rxln=MRP5Mm6xNHzwv]|3pFHRt;jA+axtK!.tQ8ZUS0FLU=>d`DfX7U>' );

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
