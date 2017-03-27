<?php
/**   RUWSf1)c*a$2wcq@gy
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
define('DB_NAME', 'wp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'V8 L:uyO$<lY60GoUz7Oy[<Yb_gu,P(muik>(z9d_R}@:ttb5PEe]6~nbnrv%M^[');
define('SECURE_AUTH_KEY',  'EI=cE&A@Z(%=@pKGW=B@]v0n9#O:Z)Cr6PDJ:x+T}pg)n)N3B%Stc2jq 51LAd?-');
define('LOGGED_IN_KEY',    'rxsdIngf6{6l`UJ>E$OgS$}E3!m~/gO5AuLGQ?D,9}g(.Xe[rRi0%zwUvp D_=x@');
define('NONCE_KEY',        '_ZYw t&0wpV4$:[jl6&b{My1S3RutgsW#S;wiqg>E?dU?Kr3tBT}&6z~;hG8e6Y>');
define('AUTH_SALT',        ' szvt{2i.nmi6Ry.!kxyXv gROB1og$u)xfmJy|l5(]EG@i<^/@l)HJQE1F*:zqf');
define('SECURE_AUTH_SALT', ')J_6CO-gPIA,1O* Ca6Rg3a{mNWBU.y1 *$fDr1I_o$%#FC}/Z.J|g`6-%>[~T`:');
define('LOGGED_IN_SALT',   'n~|En:JkXGhI5zviL#{CpD+m:Oe$UXjc]28_Ri=,tIu+hT=#oI()#8S#n:YZ |4W');
define('NONCE_SALT',       'siw[(slS8Z+2+%kFFr3xmNFWNiU LF$`hG2TIYq@m*aAMi@Kf}[BX^<$ NiT#xxA');

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

