<?php
/**
 * Location-rule evaluation context.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

defined( 'ABSPATH' ) || exit;

/**
 * An immutable snapshot of the current screen used to evaluate location rules.
 *
 * Keeping the context as a plain value object (rather than reading WordPress
 * globals inside the rules engine) makes {@see LocationRules} pure and unit
 * testable. The WordPress-aware factory that builds a context from the current
 * screen lives elsewhere.
 */
final class LocationContext {

	/**
	 * Context values keyed by parameter name.
	 *
	 * @var array<string, mixed>
	 */
	private array $values;

	/**
	 * Build a context from a set of values.
	 *
	 * @param array<string, mixed> $values Context values keyed by parameter name.
	 */
	public function __construct( array $values = array() ) {
		$this->values = $values;
	}

	/**
	 * Retrieve a context value.
	 *
	 * @param string $key      Parameter name.
	 * @param mixed  $fallback Value returned when the key is absent.
	 * @return mixed
	 */
	public function get( string $key, $fallback = null ) {
		return $this->values[ $key ] ?? $fallback;
	}

	/**
	 * Retrieve a context value coerced to a list of strings.
	 *
	 * @param string $key Parameter name.
	 * @return string[]
	 */
	public function get_list( string $key ): array {
		$value = $this->values[ $key ] ?? array();

		if ( ! is_array( $value ) ) {
			$value = array( $value );
		}

		return array_values( array_map( 'strval', $value ) );
	}

	/**
	 * Whether the context has a non-empty value for a key.
	 *
	 * @param string $key Parameter name.
	 * @return bool
	 */
	public function has( string $key ): bool {
		return isset( $this->values[ $key ] ) && '' !== $this->values[ $key ];
	}

	/**
	 * Return all context values.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return $this->values;
	}
}
