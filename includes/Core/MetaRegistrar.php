<?php
/**
 * Registration of field-value post meta.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Registers field-value meta keys with a REST-safe schema.
 *
 * `register_meta()` accepts only scalar types (`string`, `integer`, `number`,
 * `boolean`) — never `array`/`object`. Complex field values are stored as
 * JSON-serialized strings; their structure is described through
 * `show_in_rest => ['schema' => ...]`, not through the meta `type`. All keys are
 * public (no `_` prefix); write access is gated per-post via `auth_callback`.
 */
final class MetaRegistrar {

	/**
	 * Scalar types accepted by {@see register_meta()}.
	 *
	 * @var string[]
	 */
	private const SCALAR_TYPES = array( 'string', 'integer', 'number', 'boolean' );

	/**
	 * Register all known field-value meta keys.
	 *
	 * Field-value meta is registered per field group; the group registry that
	 * feeds this method is introduced in a later milestone. The method is hooked
	 * now so the wiring is in place.
	 *
	 * @return void
	 */
	public function register(): void {
		/**
		 * Fires when field-value meta keys should be registered.
		 *
		 * @since 0.1.0
		 *
		 * @param MetaRegistrar $registrar The meta registrar instance.
		 */
		do_action( 'openfields/register_meta', $this );
	}

	/**
	 * Register a single field-value meta key.
	 *
	 * @param string               $object_type Meta object type (e.g. "post").
	 * @param string               $meta_key    Meta key.
	 * @param array<string, mixed> $schema      {
	 *     Optional schema overrides.
	 *
	 *     @type string        $type              Scalar type; forced to "string" if non-scalar.
	 *     @type string        $description       Human-readable description.
	 *     @type array|null    $rest_schema       Value passed to show_in_rest['schema'].
	 *     @type callable|null  $sanitize_callback Sanitization callback.
	 * }
	 * @return void
	 */
	public function register_field_meta( string $object_type, string $meta_key, array $schema = array() ): void {
		$type = isset( $schema['type'] ) && in_array( $schema['type'], self::SCALAR_TYPES, true )
			? (string) $schema['type']
			: 'string';

		$show_in_rest = true;

		if ( isset( $schema['rest_schema'] ) && is_array( $schema['rest_schema'] ) ) {
			$show_in_rest = array( 'schema' => $schema['rest_schema'] );
		}

		$args = array(
			'single'        => true,
			'type'          => $type,
			'description'   => isset( $schema['description'] ) ? (string) $schema['description'] : '',
			'show_in_rest'  => $show_in_rest,
			'auth_callback' => static function ( $allowed, $meta_key, $object_id, $user_id ) {
				unset( $allowed, $meta_key );

				return user_can( (int) $user_id, 'edit_post', (int) $object_id );
			},
		);

		if ( isset( $schema['sanitize_callback'] ) && is_callable( $schema['sanitize_callback'] ) ) {
			$args['sanitize_callback'] = $schema['sanitize_callback'];
		}

		register_meta( $object_type, $meta_key, $args );
	}
}
