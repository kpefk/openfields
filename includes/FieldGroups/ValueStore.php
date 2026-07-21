<?php
/**
 * Storage for field values in post meta.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

use OpenFields\FieldTypes\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Sanitizes and persists field values, and reads them back.
 *
 * Values are stored in post meta keyed by the field's `name`. A reference meta
 * (`_openfields_{name}`) records the field key so the value's type can be
 * resolved later. Complex values are serialized by WordPress meta storage; the
 * ACF-compatible `field_N_subfield` key scheme arrives with the Repeater field.
 */
final class ValueStore {

	/**
	 * Field type registry used to sanitize values by type.
	 *
	 * @var FieldTypeRegistry
	 */
	private FieldTypeRegistry $types;

	/**
	 * Build the value store with its field type registry.
	 *
	 * @param FieldTypeRegistry $types Field type registry.
	 */
	public function __construct( FieldTypeRegistry $types ) {
		$this->types = $types;
	}

	/**
	 * Save a group's field values for a post.
	 *
	 * @param int                  $post_id Post ID.
	 * @param FieldGroup           $group   Field group.
	 * @param array<string, mixed> $values  Raw values keyed by field name.
	 * @return void
	 */
	public function save( int $post_id, FieldGroup $group, array $values ): void {
		foreach ( $group->fields() as $field ) {
			if ( ! is_array( $field ) || ! isset( $field['name'], $field['type'] ) ) {
				continue;
			}

			$type = $this->types->get( (string) $field['type'] );

			if ( null === $type || ! $type->has_value() ) {
				continue;
			}

			$name      = (string) $field['name'];
			$raw       = $values[ $name ] ?? null;
			$sanitized = $type->sanitize( $raw, $field );

			update_post_meta( $post_id, $name, $sanitized );

			if ( isset( $field['key'] ) ) {
				update_post_meta( $post_id, '_openfields_' . $name, (string) $field['key'] );
			}
		}
	}

	/**
	 * Read a group's field values for a post, keyed by field name.
	 *
	 * @param int        $post_id Post ID.
	 * @param FieldGroup $group   Field group.
	 * @return array<string, mixed>
	 */
	public function read( int $post_id, FieldGroup $group ): array {
		$values = array();

		foreach ( $group->fields() as $field ) {
			if ( ! is_array( $field ) || ! isset( $field['name'], $field['type'] ) ) {
				continue;
			}

			$type = $this->types->get( (string) $field['type'] );

			if ( null === $type || ! $type->has_value() ) {
				continue;
			}

			$name            = (string) $field['name'];
			$values[ $name ] = get_post_meta( $post_id, $name, true );
		}

		return $values;
	}
}
