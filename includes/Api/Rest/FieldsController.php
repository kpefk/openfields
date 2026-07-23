<?php
/**
 * REST controller for reading field values.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Api\Rest;

use OpenFields\Api\FieldResolver;

defined( 'ABSPATH' ) || exit;

/**
 * Exposes `GET /wp-json/openfields/v1/{post_type}/{id}`, returning all field
 * values for a post. Use `?format=raw` for stored values or `?format=formatted`
 * (default) for values formatted by their field types.
 *
 * Versioning: the `v1` namespace is frozen; breaking changes ship under a new
 * namespace and `v1` is supported for a documented deprecation window.
 */
final class FieldsController {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	private const REST_NAMESPACE = 'openfields/v1';

	/**
	 * Field resolver.
	 *
	 * @var FieldResolver
	 */
	private FieldResolver $resolver;

	/**
	 * Build the controller with its field resolver.
	 *
	 * @param FieldResolver $resolver Field resolver.
	 */
	public function __construct( FieldResolver $resolver ) {
		$this->resolver = $resolver;
	}

	/**
	 * Register the REST route.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			'/(?P<post_type>[\w-]+)/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_fields' ),
				'permission_callback' => array( $this, 'can_read' ),
				'args'                => array(
					'id'     => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'format' => array(
						'default' => 'formatted',
						'enum'    => array( 'raw', 'formatted' ),
					),
				),
			)
		);
	}

	/**
	 * Permission check: the post must be readable by the current user.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return bool
	 */
	public function can_read( \WP_REST_Request $request ): bool {
		$post = get_post( (int) $request['id'] );

		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		if ( 'publish' === $post->post_status ) {
			return true;
		}

		return current_user_can( 'read_post', $post->ID );
	}

	/**
	 * Return the post's field values.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_fields( \WP_REST_Request $request ) {
		$post = get_post( (int) $request['id'] );

		if ( ! $post instanceof \WP_Post || (string) $request['post_type'] !== $post->post_type ) {
			return new \WP_Error(
				'openfields_not_found',
				__( 'No post found for the given type and ID.', 'openfields' ),
				array( 'status' => 404 )
			);
		}

		$format = 'raw' !== $request['format'];
		$fields = $this->resolver->get_all( (int) $post->ID, $format );

		return rest_ensure_response(
			array(
				'id'        => (int) $post->ID,
				'post_type' => $post->post_type,
				'fields'    => $fields,
			)
		);
	}
}
