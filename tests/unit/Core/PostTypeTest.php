<?php
/**
 * Tests for the field-group post type registration.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Core;

use OpenFields\Core\PostType;
use OpenFields\Core\Security;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\Core\PostType
 */
final class PostTypeTest extends TestCase {

	public function test_register_registers_the_post_type_privately(): void {
		( new PostType() )->register();

		$call = WpStubs::first_call( 'register_post_type' );

		$this->assertSame( PostType::POST_TYPE, $call[0] );
		$this->assertFalse( $call[1]['public'] );
		$this->assertTrue( $call[1]['show_ui'] );
		$this->assertSame(
			Security::CAP_MANAGE_FIELD_GROUPS,
			$call[1]['capabilities']['edit_posts']
		);
	}

	public function test_register_status_registers_the_disabled_status(): void {
		( new PostType() )->register_status();

		$call = WpStubs::first_call( 'register_post_status' );

		$this->assertSame( PostType::STATUS_DISABLED, $call[0] );
		$this->assertIsArray( $call[1] );
		$this->assertFalse( $call[1]['public'] );
	}
}
