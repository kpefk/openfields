<?php
/**
 * True/False field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Boolean toggle field, stored as 1 or 0.
 */
final class TrueFalse extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'true_false';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'True / False', 'openfields' );
	}

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
	 * @return string
	 */
	public function get_rest_type(): string {
		return 'boolean';
	}

	/**
	 * Store the value as 1 or 0.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return int
	 */
	public function sanitize( $value, array $field = array() ) {
		return $this->to_bool( $value ) ? 1 : 0;
	}

	/**
	 * Format the stored value as a boolean.
	 *
	 * @param mixed                $value Stored value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return bool
	 */
	public function format_value( $value, array $field = array() ) {
		return $this->to_bool( $value );
	}

	/**
	 * Only a null value is considered empty; false/0 is a valid value.
	 *
	 * @param mixed $value Value to test.
	 * @return bool
	 */
	protected function is_empty( $value ): bool {
		return null === $value;
	}

	/**
	 * Coerce a mixed value to a boolean.
	 *
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	private function to_bool( $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			return 0 !== (int) $value;
		}

		return in_array( strtolower( (string) $value ), array( '1', 'true', 'yes', 'on' ), true );
	}
}
