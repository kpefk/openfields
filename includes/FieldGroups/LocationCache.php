<?php
/**
 * Cache for location-rule match results.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

defined( 'ABSPATH' ) || exit;

/**
 * Caches the result of matching location rules for a given screen context.
 *
 * Location matching runs on every `add_meta_boxes` (and similar) request, so it
 * is the hottest path in the admin. Results are cached in the object cache under
 * a versioned key; saving or deleting a field group bumps the version, which
 * atomically invalidates every cached entry without enumerating keys.
 */
final class LocationCache {

	/**
	 * Object-cache group.
	 *
	 * @var string
	 */
	private const CACHE_GROUP = 'openfields_locations';

	/**
	 * Cache key holding the current namespace version.
	 *
	 * @var string
	 */
	private const VERSION_KEY = 'version';

	/**
	 * Return a cached value for a context hash, computing and storing it on miss.
	 *
	 * @param string   $context_hash Stable hash of the screen context.
	 * @param callable $callback     Producer invoked on a cache miss.
	 * @return mixed
	 */
	public function remember( string $context_hash, callable $callback ) {
		$key    = $this->namespaced_key( $context_hash );
		$cached = wp_cache_get( $key, self::CACHE_GROUP );

		if ( false !== $cached ) {
			return $cached;
		}

		$value = $callback();
		wp_cache_set( $key, $value, self::CACHE_GROUP );

		return $value;
	}

	/**
	 * Invalidate every cached location result by bumping the namespace version.
	 *
	 * @return void
	 */
	public function invalidate(): void {
		wp_cache_set( self::VERSION_KEY, $this->version() + 1, self::CACHE_GROUP );
	}

	/**
	 * Build a version-namespaced cache key for a context hash.
	 *
	 * @param string $context_hash Context hash.
	 * @return string
	 */
	private function namespaced_key( string $context_hash ): string {
		return $this->version() . ':' . $context_hash;
	}

	/**
	 * Read (initialising if necessary) the current namespace version.
	 *
	 * @return int
	 */
	private function version(): int {
		$version = wp_cache_get( self::VERSION_KEY, self::CACHE_GROUP );

		if ( false === $version ) {
			$version = 1;
			wp_cache_set( self::VERSION_KEY, $version, self::CACHE_GROUP );
		}

		return (int) $version;
	}
}
