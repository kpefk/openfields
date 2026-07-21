<?php
/**
 * Checkbox field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

defined( 'ABSPATH' ) || exit;

/**
 * Checkbox group storing a list of selected values.
 */
final class Checkbox extends AbstractChoiceFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'checkbox';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Checkbox', 'openfields' );
	}

	/**
	 * Sanitize the value to a list of sanitized strings.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string[]
	 */
	public function sanitize( $value, array $field = array() ) {
		if ( is_array( $value ) ) {
			$values = $value;
		} elseif ( '' === $value || null === $value ) {
			$values = array();
		} else {
			$values = array( $value );
		}

		return array_values( array_map( static fn ( $item ): string => sanitize_text_field( (string) $item ), $values ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $value Non-empty value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_value( $value, array $field ) {
		return $this->validate_choice( $value, $field );
	}
}
