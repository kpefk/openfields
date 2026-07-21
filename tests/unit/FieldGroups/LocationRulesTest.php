<?php
/**
 * Tests for the LocationRules engine.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\LocationContext;
use OpenFields\FieldGroups\LocationRules;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\FieldGroups\LocationRules
 * @covers \OpenFields\FieldGroups\LocationContext
 */
final class LocationRulesTest extends TestCase {

	/**
	 * @param string $post_type Post type for the context.
	 * @return LocationContext
	 */
	private function context( string $post_type ): LocationContext {
		return new LocationContext(
			array(
				'post_type'   => $post_type,
				'user_roles'  => array( 'administrator' ),
				'post_status' => 'publish',
			)
		);
	}

	public function test_empty_location_matches_everything(): void {
		$rules = new LocationRules();

		$this->assertTrue( $rules->matches( array(), $this->context( 'post' ) ) );
	}

	public function test_equals_operator_matches(): void {
		$rules    = new LocationRules();
		$location = array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				),
			),
		);

		$this->assertTrue( $rules->matches( $location, $this->context( 'post' ) ) );
		$this->assertFalse( $rules->matches( $location, $this->context( 'page' ) ) );
	}

	public function test_not_equals_operator_matches(): void {
		$rules    = new LocationRules();
		$location = array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '!=',
					'value'    => 'page',
				),
			),
		);

		$this->assertTrue( $rules->matches( $location, $this->context( 'post' ) ) );
		$this->assertFalse( $rules->matches( $location, $this->context( 'page' ) ) );
	}

	public function test_rules_within_a_group_are_anded(): void {
		$rules    = new LocationRules();
		$location = array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				),
				array(
					'param'    => 'user_role',
					'operator' => '==',
					'value'    => 'editor',
				),
			),
		);

		// post_type matches but user_role does not -> group fails.
		$this->assertFalse( $rules->matches( $location, $this->context( 'post' ) ) );
	}

	public function test_groups_are_ored(): void {
		$rules    = new LocationRules();
		$location = array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'page',
				),
			),
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				),
			),
		);

		// First group fails, second matches -> overall match.
		$this->assertTrue( $rules->matches( $location, $this->context( 'post' ) ) );
	}

	public function test_matching_groups_filters_field_groups(): void {
		$rules = new LocationRules();

		$for_posts = FieldGroup::from_array(
			array(
				'key'      => 'group_posts',
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'post',
						),
					),
				),
			)
		);
		$for_pages = FieldGroup::from_array(
			array(
				'key'      => 'group_pages',
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'page',
						),
					),
				),
			)
		);

		$matched = $rules->matching_groups( array( $for_posts, $for_pages ), $this->context( 'post' ) );

		$this->assertCount( 1, $matched );
		$this->assertSame( 'group_posts', $matched[0]->key() );
	}
}
