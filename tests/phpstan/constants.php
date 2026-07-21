<?php
/**
 * Constant declarations for static analysis.
 *
 * These constants are defined at runtime in openfields.php; declaring them here
 * lets PHPStan resolve them without executing the bootstrap. Loaded via
 * `bootstrapFiles` in phpstan.neon.dist. Not shipped in the plugin.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

define( 'OPENFIELDS_VERSION', '0.1.0-alpha' );
define( 'OPENFIELDS_FILE', '' );
define( 'OPENFIELDS_PATH', '' );
define( 'OPENFIELDS_URL', '' );
define( 'OPENFIELDS_MIN_PHP', '8.1' );
