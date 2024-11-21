<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'abelodb' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'capital07' );

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
define( 'AUTH_KEY',         '52@([a0swu9sz5S3v*R,_N-^G+*hV5r-=h!a^~H3bqm?k}f4Lw.v,FhFt7!3#,AP' );
define( 'SECURE_AUTH_KEY',  'B%c/gQB=Y%&}7nEe3^ @Ce5>{Jn5=*[1^._@o<Lag!S[ZN[8sG}l^g =118P0*~C' );
define( 'LOGGED_IN_KEY',    'jXL%J`4e[N-zY^){WO!PmUKtbv4|rX$LGIKmQ[Qw@]6@ -Str)]p5%Nc8qY5AS2c' );
define( 'NONCE_KEY',        '0RRtat]6h.<XXj@$eBzi+AbNS#CpjeS=p]%{URZKQ5)hId|x&X&g~lYcGAOK7NLX' );
define( 'AUTH_SALT',        '2Y<cuS#@FK]11+SWP2[}r.1X~Lc_DDo<?yF{;Yr&h#=yXn%1ss[Q{edwz`]iq0m@' );
define( 'SECURE_AUTH_SALT', '1{K]B&7TF_!F%RpOFFYhGEk)`Gp?m+>.s}T4, BRZpWW)1tPTlrzpDsV]~^,vp6(' );
define( 'LOGGED_IN_SALT',   'sAI!}8b;W19a~2}HV<!{=`N!M;,XF{>$ y}<]^;flD5{73C1$;Fzz+yje8l#6]m!' );
define( 'NONCE_SALT',       ']fJat66skf0.`d<GIa4@:(Q>g;CBF=pKc+)%uiaBZP<}3$_eeiMSe5x2dC.&`!$D' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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

