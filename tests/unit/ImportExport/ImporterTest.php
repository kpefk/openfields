<?php
/**
 * Tests for the Importer.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\ImportExport;

use OpenFields\Core\PostType;
use OpenFields\ImportExport\Importer;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\ImportExport\Importer
 */
final class ImporterTest extends TestCase {

	public function test_creates_a_new_group_from_a_list(): void {
		WpStubs::set( 'get_posts', static fn () => array() );

		$inserted = array();
		WpStubs::set(
			'wp_insert_post',
			static function ( $postarr ) use ( &$inserted ): int {
				$inserted[] = $postarr;

				return 10;
			}
		);

		$json = (string) json_encode(
			array( array( 'key' => 'group_a', 'title' => 'Alpha', 'fields' => array() ) )
		);
		$keys = ( new Importer() )->from_json( $json );

		$this->assertSame( array( 'group_a' ), $keys );
		$this->assertCount( 1, $inserted );
		$this->assertSame( PostType::POST_TYPE, $inserted[0]['post_type'] );
		$this->assertSame( 'Alpha', $inserted[0]['post_title'] );
	}

	public function test_imports_a_single_group_object(): void {
		WpStubs::set( 'get_posts', static fn () => array() );
		WpStubs::set( 'wp_insert_post', static fn () => 11 );

		$json = (string) json_encode( array( 'key' => 'group_x', 'fields' => array() ) );

		$this->assertSame( array( 'group_x' ), ( new Importer() )->from_json( $json ) );
	}

	public function test_updates_an_existing_group_by_key(): void {
		$existing = new \WP_Post(
			array(
				'ID'           => 7,
				'post_type'    => PostType::POST_TYPE,
				'post_status'  => 'publish',
				'post_content' => (string) json_encode( array( 'key' => 'group_a' ) ),
			)
		);
		WpStubs::set( 'get_posts', static fn () => array( $existing ) );
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$updated = array();
		WpStubs::set(
			'wp_update_post',
			static function ( $postarr ) use ( &$updated ): int {
				$updated[] = $postarr;

				return 7;
			}
		);

		( new Importer() )->from_json(
			(string) json_encode( array( 'key' => 'group_a', 'title' => 'Renamed' ) )
		);

		$this->assertCount( 1, $updated );
		$this->assertSame( 7, $updated[0]['ID'] );
		$this->assertSame( 'Renamed', $updated[0]['post_title'] );
	}

	public function test_invalid_json_imports_nothing(): void {
		$this->assertSame( array(), ( new Importer() )->from_json( 'not-json' ) );
	}
}
