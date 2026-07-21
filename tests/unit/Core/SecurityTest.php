<?php
/**
 * Tests for the Security helper.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Core;

use OpenFields\Core\Security;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\Core\Security
 */
final class SecurityTest extends TestCase {

	public function test_create_nonce_namespaces_the_action(): void {
		WpStubs::set( 'wp_create_nonce', static fn (): string => 'abc123' );

		$security = new Security();
		$result   = $security->create_nonce( 'save_group' );

		$this->assertSame( 'abc123', $result );
		$this->assertSame( array( 'openfields_save_group' ), WpStubs::first_call( 'wp_create_nonce' ) );
	}

	public function test_verify_nonce_namespaces_the_action(): void {
		WpStubs::set( 'wp_verify_nonce', static fn (): int => 1 );

		$security = new Security();
		$result   = $security->verify_nonce( 'abc123', 'save_group' );

		$this->assertTrue( $result );
		$this->assertSame(
			array( 'abc123', 'openfields_save_group' ),
			WpStubs::first_call( 'wp_verify_nonce' )
		);
	}

	public function test_verify_nonce_returns_false_when_invalid(): void {
		WpStubs::set( 'wp_verify_nonce', static fn (): bool => false );

		$security = new Security();

		$this->assertFalse( $security->verify_nonce( 'bad', 'save_group' ) );
	}

	public function test_can_manage_field_groups_uses_current_user_by_default(): void {
		WpStubs::set( 'current_user_can', static fn (): bool => true );

		$security = new Security();

		$this->assertTrue( $security->can_manage_field_groups() );
		$this->assertSame(
			array( Security::CAP_MANAGE_FIELD_GROUPS ),
			WpStubs::first_call( 'current_user_can' )
		);
	}

	public function test_can_manage_field_groups_checks_a_specific_user(): void {
		WpStubs::set( 'user_can', static fn (): bool => false );

		$security = new Security();

		$this->assertFalse( $security->can_manage_field_groups( 42 ) );
		$this->assertSame(
			array( 42, Security::CAP_MANAGE_FIELD_GROUPS ),
			WpStubs::first_call( 'user_can' )
		);
	}
}
