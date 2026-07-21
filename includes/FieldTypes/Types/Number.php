<?php
/**
 * Number field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Numeric field with optional min/max/step.
 */
final class Number extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'number';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Number', 'openfields' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_rest_type(): string {
		return 'number';
	}

	/**
	 * Coerce the value to an int or float, or an empty string when non-numeric.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return int|float|string
	 */
	public function sanitize( $value, array $field = array() ) {
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$float = (float) $value;
		$int   = (int) $value;

		return (float) $int === $float ? $int : $float;
	}

	/**
	 * Validate that the value is numeric and within any configured bounds.
	 *
	 * @param mixed                $value Non-empty value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_value( $value, array $field ) {
		if ( ! is_numeric( $value ) ) {
			return new \WP_Error(
				'openfields_number',
				__( 'Please enter a valid number.', 'openfields' )
			);
		}

		$number   = (float) $value;
		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();

		if ( isset( $settings['min'] ) && '' !== $settings['min'] && $number < (float) $settings['min'] ) {
			return new \WP_Error(
				'openfields_min',
				sprintf(
					/* translators: %s: minimum allowed value. */
					__( 'Value must be at least %s.', 'openfields' ),
					(string) $settings['min']
				)
			);
		}

		if ( isset( $settings['max'] ) && '' !== $settings['max'] && $number > (float) $settings['max'] ) {
			return new \WP_Error(
				'openfields_max',
				sprintf(
					/* translators: %s: maximum allowed value. */
					__( 'Value must be at most %s.', 'openfields' ),
					(string) $settings['max']
				)
			);
		}

		return true;
	}
}
