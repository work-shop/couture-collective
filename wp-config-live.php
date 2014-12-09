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
define('DB_NAME', 'wscc_devdb');

/** MySQL database username */
define('DB_USER', 'ccdevuser');

/** MySQL database password */
define('DB_PASSWORD', 'ctrcllctve');

/** MySQL hostname */
define('DB_HOST', 'wp-cc-dev-db.cznneelgqdqs.us-east-1.rds.amazonaws.com:3306');

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
define('AUTH_KEY',         'A]T/@r%50&&AKBLAq1Fgn,$ibM&z`4+9H;1/xH%pQr|hV8YJGP-H$lvAAs)RFEz`');
define('SECURE_AUTH_KEY',  'jp`d5u=q+b/RrQ.U@|Zr{d)`T_*gY~ms T[z`0FD?*PlL%g/{Gid*KTCv0HBQb] ');
define('LOGGED_IN_KEY',    'm6l+4+1ylZT~=s!I]]cU(MI7@eZtA+aqk-Kyeyhoof-zwYi/,7dahG,zc&2>-3Je');
define('NONCE_KEY',        'b<:?azd8-|_gS{w`I]P|4nb(Rd3^Q:-yWVop*(XD-_UfJ(d&wwq` :Cq<*2v(5]z');
define('AUTH_SALT',        '~HD%g{P9U<+8/-?H_Q|,FPbbjP5I{Q&,r;p=t)lLzN8uvENUNz?*mLK`M@rdDza3');
define('SECURE_AUTH_SALT', 'U5%mmnAI+*c+&];nmY=/dEcpo-s-L!`t;y<1mO0Fe[)*uy<%6G0MaR|y;C?g#a^1');
define('LOGGED_IN_SALT',   'Nv[7ii^)3Tuo_N$[G$lXf|fj}){2KgqAZ)v#!? Y^.KhS<ao*H=XcQK?88Z72>KB');
define('NONCE_SALT',       '7h+T-=PlqYZBL`m{gfVW7Qb!| nXS})O@.4addFz!_$<f+~NLvLAv-[F?Y~3hf}l');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
