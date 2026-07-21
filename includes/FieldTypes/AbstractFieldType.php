<?php
/**
 * Base class for field types.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Contract shared by every field type (built-in and third-party).
 *
 * A field type knows how to sanitize an incoming value, validate it (returning a
 * {@see \WP_Error} on failure), format a stored value for output, and describe
 * itself for the REST API and the TypeScript type generator.
 */
abstract class AbstractFieldType {

	/**
	 * The unique type identifier, e.g. "text".
	 *
	 * @return string
	 */
	abstract public function get_type(): string;

	/**
	 * The human-readable label shown in the builder.
	 *
	 * @return string
	 */
	abstract public function get_label(): string;

	/**
	 * The builder category: basic, content, choice, relational or layout.
	 *
	 * @return string
	 */
	public function get_category(): string {
		return 'basic';
	}

	/**
	 * Whether this type stores a value (false for UI-only types like Message).
	 *
	 * @return bool
	 */
	public function has_value(): bool {
		return true;
	}

	/**
	 * The REST/meta scalar type: string, integer, number or boolean.
	 *
	 * @return string
	 */
	public function get_rest_type(): string {
		return 'string';
	}

	/**
	 * Default settings for a new field of this type.
	 *
	 * @return array<string, mixed>
	 */
	public function get_default_settings(): array {
		return array();
	}

	/**
	 * Sanitize a raw value before it is stored.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return mixed
	 */
	public function sanitize( $value, array $field = array() ) {
		return sanitize_text_field( (string) $value );
	}

	/**
	 * Validate a value.
	 *
	 * Handles the shared "required" check, then delegates type-specific checks to
	 * {@see AbstractFieldType::validate_value()}.
	 *
	 * @param mixed                $value Value to validate.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error True when valid, otherwise a WP_Error.
	 */
	public function validate( $value, array $field = array() ) {
		$is_required = ! empty( $field['required'] );

		if ( $this->is_empty( $value ) ) {
			if ( $is_required ) {
				return new \WP_Error(
					'openfields_required',
					__( 'This field is required.', 'openfields' )
				);
			}

			return true;
		}

		return $this->validate_value( $value, $field );
	}

	/**
	 * Format a stored value for output.
	 *
	 * @param mixed                $value Stored value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return mixed
	 */
	public function format_value( $value, array $field = array() ) {
		return $value;
	}

	/**
	 * The REST schema describing this field's value.
	 *
	 * @return array<string, mixed>
	 */
	public function get_rest_schema(): array {
		return array( 'type' => $this->get_rest_type() );
	}

	/**
	 * The JSON schema describing this field's value, for TypeScript generation.
	 *
	 * @return array<string, mixed>
	 */
	public function get_json_schema(): array {
		return array(
			'type'  => $this->get_rest_type(),
			'title' => $this->get_label(),
		);
	}

	/**
	 * Type-specific validation, run only for non-empty values.
	 *
	 * @param mixed                $value Non-empty value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_value( $value, array $field ) {
		return true;
	}

	/**
	 * Whether a value counts as empty for the required check.
	 *
	 * @param mixed $value Value to test.
	 * @return bool
	 */
	protected function is_empty( $value ): bool {
		return null === $value || '' === $value || array() === $value;
	}
}
