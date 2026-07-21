<?php
/**
 * Tests for the MetaRegistrar.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Core;

use OpenFields\Core\MetaRegistrar;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\Core\MetaRegistrar
 */
final class MetaRegistrarTest extends TestCase {

	public function test_register_field_meta_registers_single_scalar_meta(): void {
		( new MetaRegistrar() )->register_field_meta(
			'post',
			'openfields_headline',
			array( 'type' => 'string' )
		);

		$call = WpStubs::first_call( 'register_meta' );

		$this->assertSame( 'post', $call[0] );
		$this->assertSame( 'openfields_headline', $call[1] );
		$this->assertTrue( $call[2]['single'] );
		$this->assertSame( 'string', $call[2]['type'] );
		$this->assertTrue( $call[2]['show_in_rest'] );
		$this->assertIsCallable( $call[2]['auth_callback'] );
	}

	public function test_non_scalar_type_is_forced_to_string(): void {
		( new MetaRegistrar() )->register_field_meta(
			'post',
			'openfields_repeater',
			array(
				'type'        => 'array',
				'rest_schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
			)
		);

		$args = WpStubs::first_call( 'register_meta' )[2];

		$this->assertSame( 'string', $args['type'] );
		$this->assertIsArray( $args['show_in_rest'] );
		$this->assertArrayHasKey( 'schema', $args['show_in_rest'] );
		$this->assertSame( 'array', $args['show_in_rest']['schema']['type'] );
	}
}
