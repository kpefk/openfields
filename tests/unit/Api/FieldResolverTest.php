<?php
/**
 * Tests for the FieldResolver.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Api;

use OpenFields\Api\FieldResolver;
use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldGroups\LocalStore;
use OpenFields\FieldGroups\SchemaUpgrader;
use OpenFields\FieldTypes\FieldTypeRegistry;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\Api\FieldResolver
 */
final class FieldResolverTest extends TestCase {

	/**
	 * Build a resolver over a single locally-registered group.
	 *
	 * @return FieldResolver
	 */
	private function resolver(): FieldResolver {
		WpStubs::set( 'get_posts', static fn () => array() );

		$local = new LocalStore();
		$local->add(
			array(
				'key'    => 'group_a',
				'fields' => array(
					array(
						'key'  => 'field_h',
						'name' => 'headline',
						'type' => 'text',
					),
					array(
						'key'  => 'field_c',
						'name' => 'count',
						'type' => 'number',
					),
				),
			)
		);

		$registry = new FieldTypeRegistry();
		$registry->register_defaults();

		return new FieldResolver(
			new FieldGroupRepository( new SchemaUpgrader(), $local ),
			$registry
		);
	}

	public function test_get_reads_value_by_name_and_by_key(): void {
		WpStubs::set(
			'get_post_meta',
			static fn ( $post_id, $key ) => 'headline' === $key ? 'Hello' : ''
		);

		$resolver = $this->resolver();

		$this->assertSame( 'Hello', $resolver->get( 'headline', 10, false ) );
		$this->assertSame( 'Hello', $resolver->get( 'field_h', 10, false ) );
	}

	public function test_get_returns_null_for_unknown_field(): void {
		WpStubs::set( 'get_post_meta', static fn () => '' );

		$this->assertNull( $this->resolver()->get( 'missing', 10 ) );
	}

	public function test_update_sanitizes_and_stores(): void {
		$captured = array();
		WpStubs::set(
			'update_post_meta',
			static function ( $post_id, $key, $value ) use ( &$captured ): bool {
				$captured[ $key ] = $value;

				return true;
			}
		);

		$resolver = $this->resolver();

		$this->assertTrue( $resolver->update( 'headline', '  Hi  ', 10 ) );
		$this->assertSame( 'Hi', $captured['headline'] );
		$this->assertFalse( $resolver->update( 'missing', 'x', 10 ) );
	}

	public function test_get_all_returns_values_keyed_by_name(): void {
		WpStubs::set(
			'get_post_meta',
			static fn ( $post_id, $key ) => 'headline' === $key ? 'Hi' : '3'
		);

		$all = $this->resolver()->get_all( 10 );

		$this->assertArrayHasKey( 'headline', $all );
		$this->assertArrayHasKey( 'count', $all );
		$this->assertSame( 'Hi', $all['headline'] );
	}
}
