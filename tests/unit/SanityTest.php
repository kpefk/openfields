<?php
/**
 * Sanity test — verifies the unit test harness and autoloader are wired up.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class SanityTest extends TestCase {

	public function test_test_harness_runs(): void {
		$this->assertTrue( true );
	}

	public function test_composer_autoload_maps_test_namespace(): void {
		$this->assertStringContainsString(
			'OpenFields\\Tests\\Unit',
			static::class
		);
	}
}
