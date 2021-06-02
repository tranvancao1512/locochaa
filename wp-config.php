<?php
define('SAVEQUERIES', true);
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
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
define( 'DB_NAME', 'truemag' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );




/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'S]:OwKlu;(OO, cSA)eL2Q&*q]`Shu-tr& $%Pq:~R%nT}.!z42[>S;|^5eMu;QV' );
define( 'SECURE_AUTH_KEY',  ';>F^d,@g%>x@Q3%^Z?h2[`.D,8`g{xa&M5 _YlLXnq$C]st-!BX9HD6%BnftHJi#' );
define( 'LOGGED_IN_KEY',    '}S#&<(|^4b] )ESmb1OMIS~]n_Qn0v#|z7b(Q`dIsEOpPm.1|&zFF1(d-TZpt7$p' );
define( 'NONCE_KEY',        'H@L9lN0NocKx}CaSa.fm<0`D**P`GOi_`(QBqB0 ;=Yj0b46,tl cktD) ^1K|Y]' );
define( 'AUTH_SALT',        '(fai?o~*U(ueor`+@~0vH}Yb(OmsvKKpSM;Qo9tCGkDinSA@65rI6NXI,ka)|+Sb' );
define( 'SECURE_AUTH_SALT', 'x<fc1Us7QN>3U!^BP%]Zn$)c,PV/+,V.T3~+%wb`S@ 6oj%{Yt#1pJIR-df2S$(`' );
define( 'LOGGED_IN_SALT',   ' 2M~?b.`12[;}>yVB*YY.ElV8JZ,j2smqlmrVrsD4&1[}h{9[z0}3Z`1(5]ZGT@D' );
define( 'NONCE_SALT',       'x@7TK2A#p2c?~s+W$od^G!(/M6J#BaUu~~G^cl6t~QM&ZMC;y1a(@!q0s|%q}%[+' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
