<?php
/**
 * File field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * File field storing a media-library attachment ID.
 */
final class File extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'file';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'File', 'openfields' );
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
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_rest_type(): string {
		return 'integer';
	}

	/**
	 * Store the attachment ID as a positive integer.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return int
	 */
	public function sanitize( $value, array $field = array() ) {
		return absint( $value );
	}

	/**
	 * Format the stored value as an attachment ID, or null when unset.
	 *
	 * @param mixed                $value Stored value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return int|null
	 */
	public function format_value( $value, array $field = array() ) {
		$id = absint( $value );

		return $id > 0 ? $id : null;
	}
}
