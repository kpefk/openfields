<?php
/**
 * Email field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Email field with address validation.
 */
final class Email extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'email';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Email', 'openfields' );
	}

	/**
	 * Sanitize the value as an email address.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string
	 */
	public function sanitize( $value, array $field = array() ) {
		return sanitize_email( (string) $value );
	}

	/**
	 * Validate the value is a well-formed email address.
	 *
	 * @param mixed                $value Non-empty value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_value( $value, array $field ) {
		if ( false === is_email( (string) $value ) ) {
			return new \WP_Error(
				'openfields_email',
				__( 'Please enter a valid email address.', 'openfields' )
			);
		}

		return true;
	}
}
