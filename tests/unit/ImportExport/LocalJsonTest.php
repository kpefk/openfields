<?php
/**
 * Tests for Local JSON loading.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\ImportExport;

use OpenFields\FieldGroups\LocalStore;
use OpenFields\ImportExport\LocalJson;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\ImportExport\LocalJson
 */
final class LocalJsonTest extends TestCase {

	/**
	 * Temporary directory created for a test.
	 *
	 * @var string
	 */
	private string $dir = '';

	protected function tearDown(): void {
		if ( '' !== $this->dir && is_dir( $this->dir ) ) {
			array_map( 'unlink', (array) glob( $this->dir . '/*' ) );
			rmdir( $this->dir );
		}
		parent::tearDown();
	}

	public function test_load_directory_registers_group_files(): void {
		$this->dir = sys_get_temp_dir() . '/openfields-local-' . uniqid();
		mkdir( $this->dir );
		file_put_contents(
			$this->dir . '/group_a.json',
			(string) json_encode( array( 'key' => 'group_a', 'title' => 'Alpha' ) )
		);

		$store = new LocalStore();
		( new LocalJson( $store ) )->load_directory( $this->dir );

		$this->assertTrue( $store->has( 'group_a' ) );
		$this->assertSame( 'Alpha', $store->all()[0]->title() );
	}

	public function test_missing_directory_is_ignored(): void {
		$store = new LocalStore();
		( new LocalJson( $store ) )->load_directory( '/does/not/exist' );

		$this->assertSame( array(), $store->all() );
	}
}
