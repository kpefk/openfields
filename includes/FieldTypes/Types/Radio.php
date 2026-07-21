<?php
/**
 * Radio button field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

defined( 'ABSPATH' ) || exit;

/**
 * Radio button group storing a single selected value.
 */
final class Radio extends AbstractChoiceFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'radio';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Radio Button', 'openfields' );
	}

	/**
	 * Sanitize the single selected value.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string
	 */
	public function sanitize( $value, array $field = array() ) {
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
