<?php
/**
 * Registry backing the namespaced WordPress function stubs.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests;

/**
 * Records calls to stubbed WordPress functions and dispatches to per-test
 * handlers.
 *
 * The stub functions live in the OpenFields\Core namespace (see
 * tests/stubs/wp-functions.php); unqualified calls inside the classes under
 * test resolve to those stubs, which delegate here. Reset between tests.
 */
final class WpStubs {

	/**
	 * Registered handlers keyed by function name.
	 *
	 * @var array<string, callable>
	 */
	private static array $handlers = array();

	/**
	 * Recorded calls keyed by function name.
	 *
	 * @var array<string, array<int, array<int, mixed>>>
	 */
	private static array $calls = array();

	/**
	 * Register a handler for a stubbed function.
	 *
	 * @param string   $function Function name.
	 * @param callable $handler  Handler receiving the call arguments.
	 * @return void
	 */
	public static function set( string $function, callable $handler ): void {
		self::$handlers[ $function ] = $handler;
	}

	/**
	 * Dispatch a stubbed function call and record it.
	 *
	 * @param string             $function Function name.
	 * @param array<int, mixed>  $args     Call arguments.
	 * @return mixed
	 */
	public static function invoke( string $function, array $args ) {
		self::$calls[ $function ][] = $args;

		if ( isset( self::$handlers[ $function ] ) ) {
			return ( self::$handlers[ $function ] )( ...$args );
		}

		return null;
	}

	/**
	 * Retrieve recorded calls for a function.
	 *
	 * @param string $function Function name.
	 * @return array<int, array<int, mixed>>
	 */
	public static function calls( string $function ): array {
		return self::$calls[ $function ] ?? array();
	}

	/**
	 * Retrieve the arguments of the first recorded call for a function.
	 *
	 * @param string $function Function name.
	 * @return array<int, mixed>
	 */
	public static function first_call( string $function ): array {
		return self::calls( $function )[0] ?? array();
	}

	/**
	 * Clear all handlers and recorded calls.
	 *
	 * @return void
	 */
	public static function reset(): void {
		self::$handlers = array();
		self::$calls    = array();
	}
}
