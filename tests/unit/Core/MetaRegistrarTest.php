<?php
/**
 * Tests for the MetaRegistrar.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Core;

use OpenFields\Core\MetaRegistrar;
use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldGroups\LocalStore;
use OpenFields\FieldGroups\SchemaUpgrader;
use OpenFields\FieldTypes\FieldTypeRegistry;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\Core\MetaRegistrar
 */
final class MetaRegistrarTest extends TestCase {

	public function test_register_registers_scalar_meta_per_value_field(): void {
		WpStubs::set( 'get_posts', static fn () => array() );

		$local = new LocalStore();
		$local->add(
			array(
				'key'    => 'group_a',
				'fields' => array(
					array( 'key' => 'field_h', 'name' => 'headline', 'type' => 'text' ),
					array( 'key' => 'field_c', 'name' => 'count', 'type' => 'number' ),
					array( 'key' => 'field_t', 'name' => 'toggle', 'type' => 'true_false' ),
					array( 'key' => 'field_m', 'name' => 'note', 'type' => 'message' ),
				),
			)
		);

		$registered = array();
		WpStubs::set(
			'register_meta',
			static function ( $object_type, $meta_key, $args ) use ( &$registered ): bool {
				$registered[ $meta_key ] = $args;

				return true;
			}
		);

		$registry = new FieldTypeRegistry();
		$registry->register_defaults();

		$meta = new MetaRegistrar(
			new FieldGroupRepository( new SchemaUpgrader(), $local ),
			$registry
		);
		$meta->register();

		$this->assertArrayHasKey( 'headline', $registered );
		$this->assertSame( 'string', $registered['headline']['type'] );
		$this->assertTrue( $registered['headline']['single'] );
		$this->assertTrue( $registered['headline']['show_in_rest'] );

		$this->assertSame( 'number', $registered['count']['type'] );
		$this->assertSame( 'boolean', $registered['toggle']['type'] );

		// UI-only fields (Message) register no meta.
		$this->assertArrayNotHasKey( 'note', $registered );
	}
}
