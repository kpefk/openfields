<?php
/**
 * Text field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Single-line text field.
 */
final class Text extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'text';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Text', 'openfields' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array<string, mixed>
	 */
	public function get_default_settings(): array {
		return array(
			'default_value' => '',
			'placeholder'   => '',
			'maxlength'     => 0,
		);
	}

	/**
	 * Reject values longer than the configured maximum length.
	 *
	 * @param mixed                $value Non-empty value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true|\WP_Error
	 */
	protected function validate_value( $value, array $field ) {
		$settings  = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		$maxlength = isset( $settings['maxlength'] ) ? (int) $settings['maxlength'] : 0;

		if ( $maxlength > 0 && mb_strlen( (string) $value ) > $maxlength ) {
			return new \WP_Error(
				'openfields_maxlength',
				sprintf(
					/* translators: %d: maximum number of characters. */
					__( 'Value may not exceed %d characters.', 'openfields' ),
					$maxlength
				)
			);
		}

		return true;
	}
}
