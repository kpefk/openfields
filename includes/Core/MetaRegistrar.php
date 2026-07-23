<?php
/**
 * Registration of field-value post meta.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldTypes\AbstractFieldType;
use OpenFields\FieldTypes\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Registers field-value meta keys with a REST-safe schema, so values are
 * available to the REST API and the Gutenberg editor (via `@wordpress/core-data`).
 *
 * `register_meta()` accepts only scalar types; all keys are public (no `_`
 * prefix), sanitized by the field type, and writable per-post via `auth_callback`.
 */
final class MetaRegistrar {

	/**
	 * Scalar types accepted by {@see register_meta()}.
	 *
	 * @var string[]
	 */
	private const SCALAR_TYPES = array( 'string', 'integer', 'number', 'boolean' );

	/**
	 * Field group repository.
	 *
	 * @var FieldGroupRepository
	 */
	private FieldGroupRepository $repository;

	/**
	 * Field type registry.
	 *
	 * @var FieldTypeRegistry
	 */
	private FieldTypeRegistry $types;

	/**
	 * Build the registrar with its collaborators.
	 *
	 * @param FieldGroupRepository $repository Field group repository.
	 * @param FieldTypeRegistry    $types      Field type registry.
	 */
	public function __construct( FieldGroupRepository $repository, FieldTypeRegistry $types ) {
		$this->repository = $repository;
		$this->types      = $types;
	}

	/**
	 * Register meta for every value field across all active groups.
	 *
	 * @return void
	 */
	public function register(): void {
		$registered = array();

		foreach ( $this->repository->active() as $group ) {
			foreach ( $group->fields() as $field ) {
				if ( ! is_array( $field ) || ! isset( $field['name'], $field['type'] ) ) {
					continue;
				}

				$name = (string) $field['name'];

				if ( isset( $registered[ $name ] ) ) {
					continue;
				}

				$type = $this->types->get( (string) $field['type'] );

				if ( null === $type || ! $type->has_value() ) {
					continue;
				}

				$this->register_field( $name, $type, $field );
				$registered[ $name ] = true;
			}
		}

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
	 * Register a single field's value meta.
	 *
	 * @param string               $name  Meta key (field name).
	 * @param AbstractFieldType    $type  Field type.
	 * @param array<string, mixed> $field Field configuration.
	 * @return void
	 */
	public function register_field( string $name, AbstractFieldType $type, array $field ): void {
		$rest_type = in_array( $type->get_rest_type(), self::SCALAR_TYPES, true )
			? $type->get_rest_type()
			: 'string';

		register_meta(
			'post',
			$name,
			array(
				'single'            => true,
				'type'              => $rest_type,
				'show_in_rest'      => true,
				'sanitize_callback' => static fn ( $value ) => $type->sanitize( $value, $field ),
				'auth_callback'     => static function ( $allowed, $meta_key, $object_id, $user_id ) {
					unset( $allowed, $meta_key );

					return user_can( (int) $user_id, 'edit_post', (int) $object_id );
				},
			)
		);
	}
}
