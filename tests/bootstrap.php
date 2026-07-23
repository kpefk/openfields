<?php
/**
 * PHPUnit bootstrap for OpenFields unit tests.
 *
 * Unit tests run against plain classes with WordPress functions stubbed via
 * namespaced overrides (see tests/stubs/wp-functions.php), so no full WordPress
 * test suite is required. Integration tests that need WordPress run separately
 * via wp-env / WP-CLI.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! is_readable( $autoloader ) ) {
	fwrite( STDERR, "Composer autoloader not found. Run `composer install`.\n" );
	exit( 1 );
}

/*
 * Plugin source files guard against direct access with `defined( 'ABSPATH' ) ||
 * exit;`. Define ABSPATH (and the plugin constants defined at runtime in
 * openfields.php) BEFORE the autoloader, because the `files` autoload entry
 * (includes/Api/functions.php) runs that guard as it loads.
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', sys_get_temp_dir() . '/' );
}

if ( ! defined( 'OPENFIELDS_VERSION' ) ) {
	define( 'OPENFIELDS_VERSION', '0.1.0-alpha' );
	define( 'OPENFIELDS_FILE', __DIR__ . '/../openfields.php' );
	define( 'OPENFIELDS_PATH', dirname( __DIR__ ) . '/' );
	define( 'OPENFIELDS_URL', 'http://example.test/wp-content/plugins/openfields/' );
	define( 'OPENFIELDS_MIN_PHP', '8.1' );
}

require_once $autoloader;

// Namespaced WordPress function stubs used by unit tests.
require_once __DIR__ . '/stubs/wp-functions.php';

// Public API functions (loaded explicitly, not via Composer files autoload).
require_once dirname( __DIR__ ) . '/includes/Api/functions.php';
