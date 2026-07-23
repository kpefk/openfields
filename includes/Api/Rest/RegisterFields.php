<?php
/**
 * Adds field values to the standard WP REST API responses.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Api\Rest;

use OpenFields\Api\FieldResolver;

defined( 'ABSPATH' ) || exit;

/**
 * Registers an `openfields_fields` field on the standard `/wp/v2/*` post
 * endpoints, exposing each post's formatted field values.
 */
final class RegisterFields {

	/**
	 * REST response key.
	 *
	 * @var string
	 */
	private const REST_FIELD = 'openfields_fields';

	/**
	 * Field resolver.
	 *
	 * @var FieldResolver
	 */
	private FieldResolver $resolver;

	/**
	 * Build with the field resolver.
	 *
	 * @param FieldResolver $resolver Field resolver.
	 */
	public function __construct( FieldResolver $resolver ) {
		$this->resolver = $resolver;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_rest_field' ) );
	}

	/**
	 * Register the REST field for all post types with REST support.
	 *
	 * @return void
	 */
	public function register_rest_field(): void {
		$post_types = get_post_types( array( 'show_in_rest' => true ), 'names' );

		register_rest_field(
			$post_types,
			self::REST_FIELD,
			array(
				'get_callback' => array( $this, 'get_value' ),
				'schema'       => array(
					'description' => __( 'OpenFields field values.', 'openfields' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
				),
			)
		);
	}

	/**
	 * Resolve the formatted field values for a REST post object.
	 *
	 * @param array<string, mixed> $post REST-prepared post data.
	 * @return array<string, mixed>
	 */
	public function get_value( array $post ): array {
		$id = isset( $post['id'] ) ? (int) $post['id'] : 0;

		return $this->resolver->get_all( $id, true );
	}
}
