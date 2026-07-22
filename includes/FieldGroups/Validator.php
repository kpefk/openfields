<?php
/**
 * Server-side validation of field-group values.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

use OpenFields\FieldTypes\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Validates a group's submitted values against each field type, producing a
 * {@see \WP_Error} whose error codes are the field keys — the standardized
 * `{ field_key: message }` contract shared with the client record form.
 */
final class Validator {

	/**
	 * Field type registry.
	 *
	 * @var FieldTypeRegistry
	 */
	private FieldTypeRegistry $types;

	/**
	 * Build the validator with its field type registry.
	 *
	 * @param FieldTypeRegistry $types Field type registry.
	 */
	public function __construct( FieldTypeRegistry $types ) {
		$this->types = $types;
	}

	/**
	 * Validate a group's values.
	 *
	 * @param FieldGroup           $group  Field group.
	 * @param array<string, mixed> $values Raw values keyed by field name.
	 * @return \WP_Error Error codes are field keys; empty when everything is valid.
	 */
	public function validate( FieldGroup $group, array $values ): \WP_Error {
		$errors = new \WP_Error();

		foreach ( $group->fields() as $field ) {
			if ( ! is_array( $field ) || ! isset( $field['name'], $field['type'] ) ) {
				continue;
			}

			$type = $this->types->get( (string) $field['type'] );

			if ( null === $type || ! $type->has_value() ) {
				continue;
			}

			$name   = (string) $field['name'];
			$result = $type->validate( $values[ $name ] ?? null, $field );

			if ( is_wp_error( $result ) ) {
				$key = isset( $field['key'] ) ? (string) $field['key'] : $name;
				$errors->add( $key, $result->get_error_message() );
			}
		}

		return $errors;
	}

	/**
	 * Convert a validation error into a `{ field_key: message }` map.
	 *
	 * @param \WP_Error $error Validation error.
	 * @return array<string, string>
	 */
	public static function to_map( \WP_Error $error ): array {
		$map = array();

		foreach ( $error->get_error_codes() as $code ) {
			$map[ (string) $code ] = (string) $error->get_error_message( $code );
		}

		return $map;
	}
}
