<?php
/**
 * Tests for the REST FieldsController.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Api;

use OpenFields\Api\FieldResolver;
use OpenFields\Api\Rest\FieldsController;
use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldGroups\LocalStore;
use OpenFields\FieldGroups\SchemaUpgrader;
use OpenFields\FieldTypes\FieldTypeRegistry;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\Api\Rest\FieldsController
 */
final class FieldsControllerTest extends TestCase {

	/**
	 * Build a controller over a single locally-registered group.
	 *
	 * @return FieldsController
	 */
	private function controller(): FieldsController {
		WpStubs::set( 'get_posts', static fn () => array() );

		$local = new LocalStore();
		$local->add(
			array(
				'key'    => 'group_a',
				'fields' => array(
					array( 'key' => 'field_h', 'name' => 'headline', 'type' => 'text' ),
				),
			)
		);

		$registry = new FieldTypeRegistry();
		$registry->register_defaults();

		return new FieldsController(
			new FieldResolver(
				new FieldGroupRepository( new SchemaUpgrader(), $local ),
				$registry
			)
		);
	}

	public function test_can_read_allows_published_posts(): void {
		WpStubs::set(
			'get_post',
			static fn () => new \WP_Post(
				array( 'ID' => 5, 'post_status' => 'publish', 'post_type' => 'post' )
			)
		);

		$request = new \WP_REST_Request( array( 'id' => 5 ) );

		$this->assertTrue( $this->controller()->can_read( $request ) );
	}

	public function test_can_read_denies_private_posts_without_capability(): void {
		WpStubs::set(
			'get_post',
			static fn () => new \WP_Post(
				array( 'ID' => 5, 'post_status' => 'private', 'post_type' => 'post' )
			)
		);
		WpStubs::set( 'current_user_can', static fn () => false );

		$request = new \WP_REST_Request( array( 'id' => 5 ) );

		$this->assertFalse( $this->controller()->can_read( $request ) );
	}

	public function test_get_fields_returns_values(): void {
		WpStubs::set(
			'get_post',
			static fn () => new \WP_Post(
				array( 'ID' => 5, 'post_status' => 'publish', 'post_type' => 'post' )
			)
		);
		WpStubs::set(
			'get_post_meta',
			static fn ( $post_id, $key ) => 'headline' === $key ? 'Hi' : ''
		);

		$request  = new \WP_REST_Request(
			array( 'id' => 5, 'post_type' => 'post', 'format' => 'formatted' )
		);
		$response = $this->controller()->get_fields( $request );

		$this->assertInstanceOf( \WP_REST_Response::class, $response );

		$data = $response->get_data();
		$this->assertSame( 5, $data['id'] );
		$this->assertSame( 'post', $data['post_type'] );
		$this->assertSame( 'Hi', $data['fields']['headline'] );
	}

	public function test_get_fields_returns_404_for_mismatched_type(): void {
		WpStubs::set(
			'get_post',
			static fn () => new \WP_Post(
				array( 'ID' => 5, 'post_status' => 'publish', 'post_type' => 'page' )
			)
		);

		$request  = new \WP_REST_Request( array( 'id' => 5, 'post_type' => 'post' ) );
		$response = $this->controller()->get_fields( $request );

		$this->assertInstanceOf( \WP_Error::class, $response );
	}
}
