<?php
/**
 * Select field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

defined( 'ABSPATH' ) || exit;

/**
 * Dropdown select field (single or multiple).
 */
final class Select extends AbstractChoiceFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'select';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Select', 'openfields' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array<string, mixed>
	 */
	public function get_default_settings(): array {
		return array(
			'choices'       => array(),
			'multiple'      => false,
			'default_value' => '',
		);
	}

	/**
	 * Sanitize a single value or a list of values.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string|string[]
	 */
	public function sanitize( $value, array $field = array() ) {
		if ( is_array( $value ) ) {
			return array_values( array_map( static fn ( $item ): string => sanitize_text_field( (string) $item ), $value ) );
		}

		return sanitize_text_field( (string) $value );
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
