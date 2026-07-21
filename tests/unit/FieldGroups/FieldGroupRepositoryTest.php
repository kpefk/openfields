<?php
/**
 * Tests for the FieldGroupRepository.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\Core\PostType;
use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\FieldGroups\FieldGroupRepository
 */
final class FieldGroupRepositoryTest extends TestCase {

	/**
	 * Build a WP_Post-like field-group post.
	 *
	 * @param int                  $id     Post ID.
	 * @param string               $status Post status.
	 * @param array<string, mixed> $config Group configuration.
	 * @return \WP_Post
	 */
	private function post( int $id, string $status, array $config ): \WP_Post {
		return new \WP_Post(
			array(
				'ID'           => $id,
				'post_type'    => PostType::POST_TYPE,
				'post_status'  => $status,
				'post_content' => (string) json_encode( $config ),
			)
		);
	}

	public function test_all_maps_posts_to_field_groups(): void {
		$posts = array(
			$this->post( 1, 'publish', array( 'key' => 'group_a', 'title' => 'A' ) ),
			$this->post( 2, PostType::STATUS_DISABLED, array( 'key' => 'group_b' ) ),
		);
		WpStubs::set( 'get_posts', static fn () => $posts );
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$groups = ( new FieldGroupRepository() )->all( true );

		$this->assertCount( 2, $groups );
		$this->assertSame( 'group_a', $groups[0]->key() );
	}

	public function test_active_excludes_disabled_groups(): void {
		$posts = array(
			$this->post( 1, 'publish', array( 'key' => 'group_a' ) ),
			$this->post( 2, PostType::STATUS_DISABLED, array( 'key' => 'group_b' ) ),
		);
		WpStubs::set( 'get_posts', static fn () => $posts );
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$active = ( new FieldGroupRepository() )->active();

		$this->assertCount( 1, $active );
		$this->assertSame( 'group_a', $active[0]->key() );
	}

	public function test_find_returns_the_matching_group(): void {
		$posts = array(
			$this->post( 1, 'publish', array( 'key' => 'group_a' ) ),
			$this->post( 2, 'publish', array( 'key' => 'group_b' ) ),
		);
		WpStubs::set( 'get_posts', static fn () => $posts );
		WpStubs::set( 'get_post', static fn ( $arg ) => $arg );

		$repository = new FieldGroupRepository();

		$this->assertSame( 'group_b', $repository->find( 'group_b' )?->key() );
		$this->assertNull( $repository->find( 'missing' ) );
	}
}
