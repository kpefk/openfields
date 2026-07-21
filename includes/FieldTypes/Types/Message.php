<?php
/**
 * Message field type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes\Types;

use OpenFields\FieldTypes\AbstractFieldType;

defined( 'ABSPATH' ) || exit;

/**
 * Informational message shown in the editor; stores no value.
 */
final class Message extends AbstractFieldType {

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'message';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Message', 'openfields' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function get_category(): string {
		return 'layout';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return bool
	 */
	public function has_value(): bool {
		return false;
	}

	/**
	 * A message stores nothing.
	 *
	 * @param mixed                $value Raw value.
	 * @param array<string, mixed> $field Field configuration.
	 * @return null
	 */
	public function sanitize( $value, array $field = array() ) {
		return null;
	}

	/**
	 * A message is always valid.
	 *
	 * @param mixed                $value Value to validate.
	 * @param array<string, mixed> $field Field configuration.
	 * @return true
	 */
	public function validate( $value, array $field = array() ) {
		return true;
	}
}
