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
define( 'AUTH_KEY',          'Rt{j#OTndWu5(&=dx))VP3*Fb|cbaZa?42k^<QvcW3sQ0W8QL`/nb@!0xT{}5-4N' );
define( 'SECURE_AUTH_KEY',   'R?LHy,/r_+X>:4S/hgo=6EILV@-Nn?$AsUhK{F>ua@f-IH[Al*=iGF^Lyz11Crf]' );
define( 'LOGGED_IN_KEY',     'R(wULc_cpzG14LWhexaGS3fN%EP`F(<>?wKPT)V0/-=*Cy.$R.v(D]rV*0Z5 );e' );
define( 'NONCE_KEY',         'cgm=a19l5?BID_We*$zo*e=I#c8i4m_{W1<0;Uoo?Qj6sc<n4#W,WU8.Z>c3fGK0' );
define( 'AUTH_SALT',         '8y6y&3ypbU;lo{n|TFN`1~,05!h9V=z2!E|]xo5WK{k0=;=caxdIhKaH=>/< r#|' );
define( 'SECURE_AUTH_SALT',  'P6k?e~?hT%YmgQc$kGVAJ0!C-vfGKd;x#/o~uf-,n?qz0;%r+s[h`VA~Zt<^Tk)c' );
define( 'LOGGED_IN_SALT',    '7b[f2c_9TJ6P$)Bd50=k*(mkQ@</HFvebKI.IdD[UNj@QaL8XC0Q{U/nl`bkE@Q5' );
define( 'NONCE_SALT',        '@X=yg87.Olv%J<U}K0V:jr;AE8[$2B[`J8S@R1NpKGb#d=@UsfdSSa<tdqo02JQF' );
define( 'WP_CACHE_KEY_SALT', 'uIYF 5J{ah,dI-3gxB^A$t[edqW{*|5u><]mm)rbsK!_?p{jEs`U/mr2V([5Q6{]' );


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
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
