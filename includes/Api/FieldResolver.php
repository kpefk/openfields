<?php
/**
 * Resolves field values for the public API.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Api;

use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldTypes\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Backs {@see get_field()}, {@see get_fields()} and {@see update_field()}.
 *
 * Field configurations are looked up by name or key from a cached map of all
 * registered groups, then values are read from / written to post meta and
 * formatted or sanitized by the field's type.
 */
final class FieldResolver {

	/**
	 * Object cache group.
	 *
	 * @var string
	 */
	private const CACHE_GROUP = 'openfields_fields';

	/**
	 * Field group repository.
	 *
	 * @var FieldGroupRepository
	 */
	private FieldGroupRepository $repository;

	/**
	 * Field type registry.
	 *
	 * @var FieldTypeRegistry
	 */
	private FieldTypeRegistry $types;

	/**
	 * Build the resolver with its collaborators.
	 *
	 * @param FieldGroupRepository $repository Field group repository.
	 * @param FieldTypeRegistry    $types      Field type registry.
	 */
	public function __construct( FieldGroupRepository $repository, FieldTypeRegistry $types ) {
		$this->repository = $repository;
		$this->types      = $types;
	}

	/**
	 * Read a field value.
	 *
	 * @param string $selector Field name or key.
	 * @param int    $post_id  Post ID.
	 * @param bool   $format   Whether to format the value via its field type.
	 * @return mixed Null when the field is unknown.
	 */
	public function get( string $selector, int $post_id, bool $format = true ) {
		$field = $this->find_field( $selector );

		if ( null === $field ) {
			return null;
		}

		$name  = isset( $field['name'] ) ? (string) $field['name'] : $selector;
		$value = get_post_meta( $post_id, $name, true );

		/**
		 * Filters a raw field value as it is loaded.
		 *
		 * @since 0.1.0
		 *
		 * @param mixed                $value    Raw stored value.
		 * @param string               $selector Field selector.
		 * @param int                  $post_id  Post ID.
		 * @param array<string, mixed> $field    Field configuration.
		 */
		$value = apply_filters( 'openfields/load_value', $value, $selector, $post_id, $field );

		if ( ! $format ) {
			return $value;
		}

		$type      = $this->types->get( isset( $field['type'] ) ? (string) $field['type'] : '' );
		$formatted = null !== $type ? $type->format_value( $value, $field ) : $value;

		/**
		 * Filters a formatted field value.
		 *
		 * @since 0.1.0
		 *
		 * @param mixed                $formatted Formatted value.
		 * @param mixed                $value     Raw value.
		 * @param string               $selector  Field selector.
		 * @param int                  $post_id   Post ID.
		 * @param array<string, mixed> $field     Field configuration.
		 */
		return apply_filters( 'openfields/format_value', $formatted, $value, $selector, $post_id, $field );
	}

	/**
	 * Read all field values for a post, keyed by field name.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string, mixed>
	 */
	public function get_all( int $post_id ): array {
		$result = array();

		foreach ( $this->field_map() as $field ) {
			if ( ! isset( $field['name'] ) ) {
				continue;
			}

			$name = (string) $field['name'];

			if ( ! isset( $result[ $name ] ) ) {
				$result[ $name ] = $this->get( $name, $post_id, true );
			}
		}

		return $result;
	}

	/**
	 * Write a field value.
	 *
	 * @param string $selector Field name or key.
	 * @param mixed  $value    Value to store.
	 * @param int    $post_id  Post ID.
	 * @return bool Whether the value was stored.
	 */
	public function update( string $selector, $value, int $post_id ): bool {
		$field = $this->find_field( $selector );

		if ( null === $field ) {
			return false;
		}

		$name      = isset( $field['name'] ) ? (string) $field['name'] : $selector;
		$type      = $this->types->get( isset( $field['type'] ) ? (string) $field['type'] : '' );
		$sanitized = null !== $type ? $type->sanitize( $value, $field ) : $value;

		$result = update_post_meta( $post_id, $name, $sanitized );

		/**
		 * Fires after a field value is written.
		 *
		 * @since 0.1.0
		 *
		 * @param string               $selector  Field selector.
		 * @param mixed                $sanitized Stored value.
		 * @param int                  $post_id   Post ID.
		 * @param array<string, mixed> $field     Field configuration.
		 */
		do_action( 'openfields/updated_value', $selector, $sanitized, $post_id, $field );

		return false !== $result;
	}

	/**
	 * Invalidate the cached field map (e.g. after a group changes).
	 *
	 * @return void
	 */
	public function invalidate(): void {
		wp_cache_set( 'map_version', $this->map_version() + 1, self::CACHE_GROUP );
	}

	/**
	 * Look up a field configuration by name or key.
	 *
	 * @param string $selector Field name or key.
	 * @return array<string, mixed>|null
	 */
	private function find_field( string $selector ): ?array {
		$map = $this->field_map();

		return $map[ $selector ] ?? null;
	}

	/**
	 * A cached map of field configurations keyed by both name and key.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function field_map(): array {
		$key    = 'map_' . $this->map_version();
		$cached = wp_cache_get( $key, self::CACHE_GROUP );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$map = array();

		foreach ( $this->repository->all( true ) as $group ) {
			foreach ( $group->fields() as $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				if ( isset( $field['name'] ) ) {
					$map[ (string) $field['name'] ] = $field;
				}
				if ( isset( $field['key'] ) ) {
					$map[ (string) $field['key'] ] = $field;
				}
			}
		}

		wp_cache_set( $key, $map, self::CACHE_GROUP );

		return $map;
	}

	/**
	 * The current field-map cache version.
	 *
	 * @return int
	 */
	private function map_version(): int {
		$version = wp_cache_get( 'map_version', self::CACHE_GROUP );

		if ( false === $version ) {
			$version = 1;
			wp_cache_set( 'map_version', $version, self::CACHE_GROUP );
		}

		return (int) $version;
	}
}
