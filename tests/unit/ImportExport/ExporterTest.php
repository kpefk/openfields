<?php
/**
 * Tests for the Exporter.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\ImportExport;

use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldGroups\LocalStore;
use OpenFields\FieldGroups\SchemaUpgrader;
use OpenFields\ImportExport\Exporter;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\ImportExport\Exporter
 */
final class ExporterTest extends TestCase {

	/**
	 * Build an exporter over two locally-registered groups.
	 *
	 * @return Exporter
	 */
	private function exporter(): Exporter {
		WpStubs::set( 'get_posts', static fn () => array() );

		$local = new LocalStore();
		$local->add( array( 'key' => 'group_a', 'title' => 'Alpha' ) );
		$local->add( array( 'key' => 'group_b', 'title' => 'Beta' ) );

		return new Exporter( new FieldGroupRepository( new SchemaUpgrader(), $local ) );
	}

	public function test_to_json_exports_all_groups(): void {
		$decoded = json_decode( $this->exporter()->to_json(), true );

		$this->assertIsArray( $decoded );
		$this->assertCount( 2, $decoded );
		$keys = array_column( $decoded, 'key' );
		$this->assertContains( 'group_a', $keys );
		$this->assertContains( 'group_b', $keys );
	}

	public function test_to_json_filters_by_key(): void {
		$decoded = json_decode( $this->exporter()->to_json( array( 'group_a' ) ), true );

		$this->assertCount( 1, $decoded );
		$this->assertSame( 'group_a', $decoded[0]['key'] );
	}

	public function test_to_php_generates_registration_code(): void {
		$php = $this->exporter()->to_php( array( 'group_a' ) );

		$this->assertStringContainsString( '<?php', $php );
		$this->assertStringContainsString( 'openfields_add_local_field_group', $php );
		$this->assertStringContainsString( 'group_a', $php );
	}
}
