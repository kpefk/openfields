<?php
/**
 * Sanitization helpers.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Shared sanitization helpers for field-group configuration.
 *
 * Field-value sanitization by field type is handled by the individual field
 * types; these helpers cover the structural configuration (keys, titles and
 * nested config arrays) that flows through the group builder and importer.
 */
final class Sanitizer {

	/**
	 * Sanitize a field or group key.
	 *
	 * Lower-cases the value and collapses each run of characters outside
	 * `[a-z0-9]` into a single underscore, trimming leading/trailing underscores.
	 *
	 * @param string $value Raw key.
	 * @return string
	 */
	public static function key( string $value ): string {
		$key = preg_replace( '/[^a-z0-9]+/', '_', strtolower( $value ) );

		return trim( null === $key ? '' : $key, '_' );
	}

	/**
	 * Sanitize a single-line text value.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function text( $value ): string {
		return sanitize_text_field( (string) $value );
	}

	/**
	 * Recursively sanitize a configuration array.
	 *
	 * Array keys are sanitized as keys; scalar string values are run through
	 * {@see sanitize_text_field()}; integers and booleans are preserved; nested
	 * arrays are sanitized recursively.
	 *
	 * @param array<string|int, mixed> $config Raw configuration.
	 * @return array<string|int, mixed>
	 */
	public static function config( array $config ): array {
		$clean = array();

		foreach ( $config as $key => $value ) {
			$clean_key = is_string( $key ) ? sanitize_key( $key ) : $key;

			if ( is_array( $value ) ) {
				$clean[ $clean_key ] = self::config( $value );
			} elseif ( is_string( $value ) ) {
				$clean[ $clean_key ] = sanitize_text_field( $value );
			} else {
				$clean[ $clean_key ] = $value;
			}
		}

		return $clean;
	}
}
