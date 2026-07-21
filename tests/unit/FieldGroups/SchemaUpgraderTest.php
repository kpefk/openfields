<?php
/**
 * Tests for the SchemaUpgrader.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\FieldGroups\SchemaUpgrader;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\FieldGroups\SchemaUpgrader
 */
final class SchemaUpgraderTest extends TestCase {

	public function test_missing_version_is_treated_as_the_first_version(): void {
		$upgrader = new SchemaUpgrader();

		$this->assertSame(
			SchemaUpgrader::FIRST_VERSION,
			$upgrader->version_of( array() )
		);
	}

	public function test_missing_version_needs_upgrade_to_be_stamped(): void {
		$upgrader = new SchemaUpgrader();

		$this->assertTrue( $upgrader->needs_upgrade( array() ) );
	}

	public function test_upgrade_stamps_the_current_version(): void {
		$upgrader = new SchemaUpgrader();

		$result = $upgrader->upgrade( array( 'title' => 'Group' ) );

		$this->assertSame( SchemaUpgrader::CURRENT_VERSION, $result['schema_version'] );
		$this->assertSame( 'Group', $result['title'] );
	}

	public function test_upgrade_is_idempotent(): void {
		$upgrader = new SchemaUpgrader();

		$once  = $upgrader->upgrade( array( 'schema_version' => SchemaUpgrader::CURRENT_VERSION ) );
		$twice = $upgrader->upgrade( $once );

		$this->assertSame( $once, $twice );
	}

	public function test_registered_upgraders_are_applied_in_sequence(): void {
		$upgrader = new SchemaUpgrader();

		$upgrader->register_upgrader(
			1,
			static function ( array $config ): array {
				$config['steps'][] = '1->2';

				return $config;
			}
		);
		$upgrader->register_upgrader(
			2,
			static function ( array $config ): array {
				$config['steps'][] = '2->3';

				return $config;
			}
		);

		$result = $upgrader->upgrade_to( array( 'schema_version' => 1 ), 3 );

		$this->assertSame( array( '1->2', '2->3' ), $result['steps'] );
		$this->assertSame( 3, $result['schema_version'] );
	}

	public function test_upgrade_to_stops_when_no_path_exists(): void {
		$upgrader = new SchemaUpgrader();
		$upgrader->register_upgrader(
			1,
			static function ( array $config ): array {
				$config['reached'] = 2;

				return $config;
			}
		);

		// Target 3 but only a 1->2 upgrader exists: stops at 2, stamps 2.
		$result = $upgrader->upgrade_to( array( 'schema_version' => 1 ), 3 );

		$this->assertSame( 2, $result['reached'] );
		$this->assertSame( 2, $result['schema_version'] );
	}
}
