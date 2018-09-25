<?php
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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'fictionaluniversity');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'o[+.*0/3TkijRkD$HPFdYo{abH$4>$SiRW>7)VB(r<wg,3H.f4,[|a{e`-aabmWA');
define('SECURE_AUTH_KEY',  'K<QgG~V]Ke(/jU0io~ 7mmH{v-*,$Z-.J*N5=Y_X3CauFW)-&R4??PmL tV.v2 u');
define('LOGGED_IN_KEY',    'x@8O{+$.hF(}KB<*~%r#lkwzD%#V*2P);!~eJu=11^{M;;THT;W5O5i?D,7*OxA.');
define('NONCE_KEY',        '5M;^U 9z~ HZ7>>5QVPv<rCNTyrA:(=&|Pq D!x3RMsab,$&h^ya$4*k3n4-)2J0');
define('AUTH_SALT',        'JiY>$tY)nHX_}1IDv5E/Xm,cFh^ELq&{A33?Xi1^Haeabr94h9|& L&Gbi1Tt!xh');
define('SECURE_AUTH_SALT', 'vyp0PM:>:V }F<M%YtFZNZ2GZ/0 n_1gRLi8yoxja!X:/Zo(/=6WW0Rq.3RV%S16');
define('LOGGED_IN_SALT',   '8_`wWXN1|=Mlv?D<DL:z,dEqs?@9gkmtAz[*qI+(*#gqYagNj.WKFeOYm-]Y2$6%');
define('NONCE_SALT',       'Uo7^)Y={aA$6n9w?q|1QK@Evm[,s.[vAm(*@@mgo-ha~CA[Rj.suH(*|PvW?3~z&');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
