<?php
/**
 * WYSIWYG editor field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Rich-text editor field.
 */
final class Wysiwyg extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'wysiwyg';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'WYSIWYG Editor', 'openfields' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_category(): string {
		return 'content';
	}

	/**
	 * Sanitize using the post-content allowed HTML.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return string
	 */
	public function sanitize( $value, array $field = array() ) {
		return wp_kses_post( (string) $value );
	}
}
