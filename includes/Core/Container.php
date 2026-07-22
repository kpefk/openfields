<?php
/**
 * Minimal dependency-injection container.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * A small PSR-11-shaped service container.
 *
 * Used only for runtime wiring of subsystems. Because subsystems rely on
 * constructor injection, unit tests instantiate them directly with mocked
 * dependencies and do not need the container.
 */
final class Container {

	/**
	 * Service factories keyed by identifier.
	 *
	 * @var array<string, callable(Container):mixed>
	 */
	private array $factories = array();

	/**
	 * Whether each service identifier is shared (resolved once).
	 *
	 * @var array<string, bool>
	 */
	private array $shared = array();

	/**
	 * Resolved shared instances keyed by identifier.
	 *
	 * @var array<string, mixed>
	 */
	private array $resolved = array();

	/**
	 * Register a factory that returns a new instance on every resolution.
	 *
	 * @param string                    $id      Service identifier.
	 * @param callable(Container):mixed $factory Factory callback.
	 * @return void
	 */
	public function bind( string $id, callable $factory ): void {
		$this->factories[ $id ] = $factory;
		$this->shared[ $id ]    = false;
		unset( $this->resolved[ $id ] );
	}

	/**
	 * Register a factory whose instance is created once and reused.
	 *
	 * @param string                    $id      Service identifier.
	 * @param callable(Container):mixed $factory Factory callback.
	 * @return void
	 */
	public function singleton( string $id, callable $factory ): void {
		$this->factories[ $id ] = $factory;
		$this->shared[ $id ]    = true;
		unset( $this->resolved[ $id ] );
	}

	/**
	 * Store an already-created instance as a shared service.
	 *
	 * @param string $id       Service identifier.
	 * @param mixed  $instance The instance to store.
	 * @return void
	 */
	public function instance( string $id, $instance ): void {
		$this->resolved[ $id ] = $instance;
		$this->shared[ $id ]   = true;
	}

	/**
	 * Determine whether a service identifier is registered.
	 *
	 * @param string $id Service identifier.
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->factories[ $id ] ) || array_key_exists( $id, $this->resolved );
	}

	/**
	 * Resolve a service by its identifier.
	 *
	 * @template T of object
	 * @param string $id Service identifier.
	 * @phpstan-param class-string<T> $id
	 * @return object
	 * @phpstan-return T
	 * @throws NotFoundException When the identifier is not registered.
	 */
	public function get( string $id ) {
		if ( array_key_exists( $id, $this->resolved ) ) {
			$resolved = $this->resolved[ $id ];
			// phpcs:ignore Generic.Commenting.DocComment.MissingShort -- Inline generic return type.
			/** @var T $resolved */
			return $resolved;
		}

		if ( ! isset( $this->factories[ $id ] ) ) {
			throw new NotFoundException(
				esc_html( sprintf( 'Service "%s" is not registered in the container.', $id ) )
			);
		}

		$object = ( $this->factories[ $id ] )( $this );

		if ( $this->shared[ $id ] ) {
			$this->resolved[ $id ] = $object;
		}

		// phpcs:ignore Generic.Commenting.DocComment.MissingShort -- Inline generic return type.
		/** @var T $object */
		return $object;
	}
}
