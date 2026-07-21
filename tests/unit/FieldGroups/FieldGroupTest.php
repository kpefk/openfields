<?php
/**
 * Tests for the FieldGroup model.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\Core\PostType;
use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\SchemaUpgrader;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\FieldGroups\FieldGroup
 */
final class FieldGroupTest extends TestCase {

	public function test_from_array_applies_defaults_and_stamps_schema_version(): void {
		$group = FieldGroup::from_array( array( 'title' => 'Hero' ) );

		$this->assertSame( 'Hero', $group->title() );
		$this->assertSame( array(), $group->fields() );
		$this->assertSame( array(), $group->location() );
		$this->assertTrue( $group->is_active() );
		$this->assertSame( SchemaUpgrader::CURRENT_VERSION, $group->schema_version() );

		$settings = $group->settings();
		$this->assertSame( 'normal', $settings['position'] );
		$this->assertSame( 'top', $settings['label_placement'] );
	}

	public function test_from_array_preserves_provided_settings(): void {
		$group = FieldGroup::from_array(
			array(
				'title'    => 'Sidebar',
				'settings' => array( 'position' => 'side' ),
			)
		);

		$this->assertSame( 'side', $group->settings()['position'] );
		// Unspecified settings still fall back to defaults.
		$this->assertSame( 'label', $group->settings()['instruction_placement'] );
	}

	public function test_from_post_reads_json_content(): void {
		$post = new \WP_Post(
			array(
				'ID'           => 12,
				'post_type'    => PostType::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => 'Fallback title',
				'post_content' => (string) json_encode(
					array(
						'title'  => 'Real title',
						'fields' => array( array( 'key' => 'field_a' ) ),
					)
				),
			)
		);
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$group = FieldGroup::from_post( $post );

		$this->assertInstanceOf( FieldGroup::class, $group );
		$this->assertSame( 'Real title', $group->title() );
		$this->assertSame( 'group_12', $group->key() );
		$this->assertCount( 1, $group->fields() );
		$this->assertTrue( $group->is_active() );
	}

	public function test_from_post_marks_disabled_status_inactive(): void {
		$post = new \WP_Post(
			array(
				'ID'           => 5,
				'post_type'    => PostType::POST_TYPE,
				'post_status'  => PostType::STATUS_DISABLED,
				'post_content' => '{}',
			)
		);
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$group = FieldGroup::from_post( $post );

		$this->assertInstanceOf( FieldGroup::class, $group );
		$this->assertFalse( $group->is_active() );
	}

	public function test_from_post_returns_null_for_other_post_types(): void {
		$post = new \WP_Post(
			array(
				'ID'        => 1,
				'post_type' => 'page',
			)
		);
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$this->assertNull( FieldGroup::from_post( $post ) );
	}
}
