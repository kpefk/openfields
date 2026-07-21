<?php
/**
 * Tests for the LocationCache.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\FieldGroups\LocationCache;
use OpenFields\Tests\Unit\TestCase;
use OpenFields\Tests\WpStubs;

/**
 * @covers \OpenFields\FieldGroups\LocationCache
 */
final class LocationCacheTest extends TestCase {

	/**
	 * Wire the wp_cache_* stubs to a local array acting as the object cache.
	 *
	 * @return void
	 */
	private function fake_object_cache(): void {
		$store = array();

		WpStubs::set(
			'wp_cache_get',
			static function ( $key, $group ) use ( &$store ) {
				return $store[ $group . ':' . $key ] ?? false;
			}
		);
		WpStubs::set(
			'wp_cache_set',
			static function ( $key, $value, $group ) use ( &$store ): bool {
				$store[ $group . ':' . $key ] = $value;

				return true;
			}
		);
	}

	public function test_remember_caches_the_producer_result(): void {
		$this->fake_object_cache();
		$cache = new LocationCache();

		$calls    = 0;
		$producer = static function () use ( &$calls ): array {
			++$calls;

			return array( 'group_a' );
		};

		$this->assertSame( array( 'group_a' ), $cache->remember( 'ctx', $producer ) );
		$this->assertSame( array( 'group_a' ), $cache->remember( 'ctx', $producer ) );
		$this->assertSame( 1, $calls, 'Producer should run once and then hit the cache.' );
	}

	public function test_invalidate_busts_the_cache(): void {
		$this->fake_object_cache();
		$cache = new LocationCache();

		$calls    = 0;
		$producer = static function () use ( &$calls ): array {
			++$calls;

			return array( 'group_a' );
		};

		$cache->remember( 'ctx', $producer );
		$cache->invalidate();
		$cache->remember( 'ctx', $producer );

		$this->assertSame( 2, $calls, 'Invalidation should force the producer to run again.' );
	}
}
