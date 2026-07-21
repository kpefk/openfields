<?php
/**
 * Base class for choice-based field types.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Shared behaviour for Select, Checkbox and Radio: a set of choices and
 * validation that the submitted value(s) are among them.
 */
abstract class AbstractChoiceFieldType extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_category(): string {
		return 'choice';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array<string, mixed>
	 */
	public function get_default_settings(): array {
		return array(
			'choices'       => array(),
			'default_value' => '',
		);
	}

	/**
	 * The allowed values (choice keys and labels) for a field.
	 *
	 * @param array<string, mixed> $field Field configuration.
	 * @return string[]
	 */
	protected function allowed_values( array $field ): array {
		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		$choices  = isset( $settings['choices'] ) && is_array( $settings['choices'] ) ? $settings['choices'] : array();

		$keys   = array_map( 'strval', array_keys( $choices ) );
		$labels = array();

		foreach ( $choices as $label ) {
			if ( is_scalar( $label ) ) {
				$labels[] = (string) $label;
			}
		}

		return array_values( array_unique( array_merge( $keys, $labels ) ) );
	}

	/**
	 * Validate that each submitted value is an allowed choice.
	 *
	 * @param mixed                $value Non-empty value (scalar or array).
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_choice( $value, array $field ) {
		$allowed = $this->allowed_values( $field );

		if ( array() === $allowed ) {
			return true;
		}

		$values = is_array( $value ) ? $value : array( $value );

		foreach ( $values as $single ) {
			if ( ! in_array( (string) $single, $allowed, true ) ) {
				return new \WP_Error(
					'openfields_choice',
					__( 'An invalid choice was selected.', 'openfields' )
				);
			}
		}

		return true;
	}
}
