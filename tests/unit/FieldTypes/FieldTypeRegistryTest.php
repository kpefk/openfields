<?php
/**
 * Tests for the FieldTypeRegistry.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldTypes;

use OpenFields\FieldTypes\FieldTypeRegistry;
use OpenFields\FieldTypes\Types\Text;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\FieldTypes\FieldTypeRegistry
 */
final class FieldTypeRegistryTest extends TestCase {

	public function test_register_and_retrieve(): void {
		$registry = new FieldTypeRegistry();
		$text     = new Text();

		$registry->register( $text );

		$this->assertTrue( $registry->has( 'text' ) );
		$this->assertSame( $text, $registry->get( 'text' ) );
		$this->assertNull( $registry->get( 'missing' ) );
	}

	public function test_register_defaults_registers_the_core_types(): void {
		$registry = new FieldTypeRegistry();
		$registry->register_defaults();

		$expected = array(
			'text',
			'textarea',
			'number',
			'email',
			'url',
			'image',
			'file',
			'wysiwyg',
			'select',
			'checkbox',
			'radio',
			'true_false',
			'message',
		);

		foreach ( $expected as $type ) {
			$this->assertTrue( $registry->has( $type ), "Missing core type: {$type}" );
		}

		$this->assertCount( 13, $registry->all() );
	}
}
