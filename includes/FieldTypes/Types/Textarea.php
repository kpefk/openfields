<?php
/**
 * Textarea field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Multi-line text field.
 */
final class Textarea extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'textarea';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Textarea', 'openfields' );
	}

	/**
	 * Sanitize while preserving line breaks.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string
	 */
	public function sanitize( $value, array $field = array() ) {
		return sanitize_textarea_field( (string) $value );
	}
}
