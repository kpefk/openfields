<?php
/**
 * Tests for the ValueStore.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\ValueStore;
use OpenFields\FieldTypes\FieldTypeRegistry;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\FieldGroups\ValueStore
 */
final class ValueStoreTest extends TestCase {

	/**
	 * Build a value store backed by the built-in field types.
	 *
	 * @return ValueStore
	 */
	private function store(): ValueStore {
		$registry = new FieldTypeRegistry();
		$registry->register_defaults();

		return new ValueStore( $registry );
	}

	/**
	 * A group with a text, number and message field.
	 *
	 * @return FieldGroup
	 */
	private function group(): FieldGroup {
		return FieldGroup::from_array(
			array(
				'fields' => array(
					array(
						'key'  => 'field_a',
						'name' => 'headline',
						'type' => 'text',
					),
					array(
						'key'  => 'field_b',
						'name' => 'count',
						'type' => 'number',
					),
					array(
						'key'  => 'field_msg',
						'name' => 'note',
						'type' => 'message',
					),
				),
			)
		);
	}

	public function test_save_sanitizes_and_stores_values(): void {
		$captured = array();
		WpStubs::set(
			'update_post_meta',
			static function ( $post_id, $key, $value ) use ( &$captured ): bool {
				$captured[ $key ] = $value;

				return true;
			}
		);

		$this->store()->save(
			10,
			$this->group(),
			array(
				'headline' => '  Hello <b>World</b>  ',
				'count'    => '5',
				'note'     => 'ignored',
			)
		);

		$this->assertSame( 'Hello World', $captured['headline'] );
		$this->assertSame( 5, $captured['count'] );
		// The message field stores no value.
		$this->assertArrayNotHasKey( 'note', $captured );
		// A reference to the field key is recorded.
		$this->assertSame( 'field_a', $captured['_openfields_headline'] );
	}

	public function test_read_returns_values_keyed_by_field_name(): void {
		WpStubs::set(
			'get_post_meta',
			static fn ( $post_id, $key ) => 'headline' === $key ? 'Hi' : ''
		);

		$values = $this->store()->read( 10, $this->group() );

		$this->assertSame( 'Hi', $values['headline'] );
		$this->assertArrayHasKey( 'count', $values );
		// The message field is not read.
		$this->assertArrayNotHasKey( 'note', $values );
	}
}
