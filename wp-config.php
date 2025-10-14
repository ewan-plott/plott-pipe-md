<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define( 'ITSEC_ENCRYPTION_KEY', 'KH46UDpSUk5QZTJfQWRiP1J3LHxdVDh4UCVbWjotbWZ8RyVjNz8rR0RAUHteeSFBSDFaM0t4eD91T1RuMTdBfg==' );


// ** Setting up env files ** //

	require_once dirname( __FILE__ ) . '/vendor/autoload.php';

	$domain = $_SERVER['HTTP_HOST'];

	if ( strpos( $domain, '.test' ) !== false || strpos( $domain, 'localhost' ) ) {
		$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__, '.env.local' );
	} else {
		$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
	}
	$dotenv->load();

		define( 'AS3CF_SETTINGS', serialize( array(
		'provider'          => 'aws',
		'access-key-id'     => $_ENV["AWS_ACCESS_KEY_ID"],
		'secret-access-key' => $_ENV["AWS_SECRET_ACCESS_KEY"],
	) ) );


#-------------------------------------------------------------------#
#                         DATABASE SETTINGS                         #
#-------------------------------------------------------------------#
	define( 'DB_NAME', $_ENV["DB_NAME"] );
	define( 'DB_USER', $_ENV["DB_USER"] );
	define( 'DB_PASSWORD', $_ENV["DB_PASS"] );
	define( 'DB_HOST', $_ENV["DB_HOST"] );
	define( 'DB_CHARSET', $_ENV["DB_CHARSET"] );
	define( 'DB_COLLATE', $_ENV["DB_COLLATE"] );

#-------------------------------------------------------------------#
#                       ENVIRONMENT SETTINGS                        #
#-------------------------------------------------------------------#
	define( 'WP_ENVIRONMENT_TYPE', $_ENV["WP_ENVIRONMENT_TYPE"] );

#-------------------------------------------------------------------#
#                         PLUGIN SETTINGS                           #
#-------------------------------------------------------------------#
	define( 'ACF_PRO_LICENSE', $_ENV["ACF_PRO_LICENSE"] );
	define( 'GF_LICENSE_KEY', $_ENV["GF_LICENSE_KEY"] );
	define( 'GFORM_DISABLE_AUTO_UPDATE', $_ENV["GFORM_DISABLE_AUTO_UPDATE"] );

#-------------------------------------------------------------------#
#                       WEBSITE SALTS SETTINGS                      #
#-------------------------------------------------------------------#
	define( 'AUTH_KEY', $_ENV["AUTH_KEY"] );
	define( 'SECURE_AUTH_KEY', $_ENV["SECURE_AUTH_KEY"] );
	define( 'LOGGED_IN_KEY', $_ENV["LOGGED_IN_KEY"] );
	define( 'NONCE_KEY', $_ENV["NONCE_KEY"] );
	define( 'AUTH_SALT', $_ENV["AUTH_SALT"] );
	define( 'SECURE_AUTH_SALT', $_ENV["SECURE_AUTH_SALT"] );
	define( 'LOGGED_IN_SALT', $_ENV["LOGGED_IN_SALT"] );
	define( 'NONCE_SALT', $_ENV["NONCE_SALT"] );

#-------------------------------------------------------------------#
#                       TABLE PREFIX SETTINGS                       #
#-------------------------------------------------------------------#
	$table_prefix = $_ENV["TABLE_PREFIX"];

#-------------------------------------------------------------------#
#                          DEBUG SETTINGS                           #
#-------------------------------------------------------------------#
	ini_set( 'display_errors', 'Off' );
	ini_set( 'error_reporting', E_ALL );
	const WP_DEBUG         = true;
	const WP_DEBUG_DISPLAY = true;

#-------------------------------------------------------------------#
#                         WP CORE SETTINGS                          #
#-------------------------------------------------------------------#
	define( 'WP_AUTO_UPDATE_CORE', $_ENV["WP_AUTO_UPDATE_CORE"] );
	define( 'FS_METHOD', 'direct' );
	define('DISABLE_WP_CRON', true);
	if ($_ENV["WP_ENVIRONMENT_TYPE"] === 'production') {
		define('DISALLOW_FILE_MODS', true);
	}

	/* That's all, stop editing! Happy publishing. */

	/** Absolute path to the WordPress directory. */
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', __DIR__ . '/' );
	}

	/** Sets up WordPress vars and included files. */
	require_once ABSPATH . 'wp-settings.php';
