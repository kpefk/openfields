<?php
/**
 * URL field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * URL field with URL validation.
 */
final class Url extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'url';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'URL', 'openfields' );
	}

	/**
	 * Sanitize the value as a URL.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string
	 */
	public function sanitize( $value, array $field = array() ) {
		return esc_url_raw( (string) $value );
	}

	/**
	 * Validate the value is a well-formed URL.
	 *
	 * @param mixed                $value Non-empty value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_value( $value, array $field ) {
		if ( false === filter_var( (string) $value, FILTER_VALIDATE_URL ) ) {
			return new \WP_Error(
				'openfields_url',
				__( 'Please enter a valid URL.', 'openfields' )
			);
		}

		return true;
	}
}
