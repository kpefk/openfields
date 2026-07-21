<?php
/**
 * PHPUnit bootstrap for OpenFields unit tests.
 *
 * Unit tests run against plain classes with mocked dependencies (constructor
 * injection), so no full WordPress test suite is required here. Integration
 * tests that need WordPress run separately via wp-env / WP-CLI.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! is_readable( $autoloader ) ) {
	fwrite( STDERR, "Composer autoloader not found. Run `composer install`.\n" );
	exit( 1 );
}

require_once $autoloader;
