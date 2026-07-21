<?php
/**
 * Field-group configuration schema upgrader.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

defined( 'ABSPATH' ) || exit;

/**
 * Migrates field-group configuration arrays between schema versions.
 *
 * Upgraders are registered per source version and applied sequentially until
 * the configuration reaches {@see SchemaUpgrader::CURRENT_VERSION}. Migration is
 * idempotent: an already-current configuration is returned unchanged (aside from
 * having its `schema_version` stamped).
 *
 * Configurations that predate versioning (no `schema_version`) are treated as
 * {@see SchemaUpgrader::FIRST_VERSION}, the initial on-disk format.
 */
final class SchemaUpgrader {

	/**
	 * The first versioned schema.
	 *
	 * @var int
	 */
	public const FIRST_VERSION = 1;

	/**
	 * The current schema version produced by this plugin build.
	 *
	 * @var int
	 */
	public const CURRENT_VERSION = 1;

	/**
	 * Registered upgraders keyed by their source version.
	 *
	 * @var array<int, callable(array<string, mixed>):array<string, mixed>>
	 */
	private array $upgraders = array();

	/**
	 * Register an upgrader that migrates a configuration from `$from_version` to
	 * `$from_version + 1`.
	 *
	 * @param int      $from_version Source schema version.
	 * @param callable $upgrader     Migration callback.
	 * @return void
	 */
	public function register_upgrader( int $from_version, callable $upgrader ): void {
		$this->upgraders[ $from_version ] = $upgrader;
	}

	/**
	 * Determine the schema version of a configuration.
	 *
	 * @param array<string, mixed> $config Configuration array.
	 * @return int
	 */
	public function version_of( array $config ): int {
		return isset( $config['schema_version'] )
			? (int) $config['schema_version']
			: self::FIRST_VERSION;
	}

	/**
	 * Whether a configuration needs upgrading.
	 *
	 * @param array<string, mixed> $config Configuration array.
	 * @return bool
	 */
	public function needs_upgrade( array $config ): bool {
		return $this->version_of( $config ) < self::CURRENT_VERSION
			|| ! isset( $config['schema_version'] );
	}

	/**
	 * Upgrade a configuration to the current schema version.
	 *
	 * @param array<string, mixed> $config Configuration array.
	 * @return array<string, mixed>
	 */
	public function upgrade( array $config ): array {
		return $this->upgrade_to( $config, self::CURRENT_VERSION );
	}

	/**
	 * Upgrade a configuration toward a target schema version.
	 *
	 * Applies each registered upgrader in sequence until the target is reached
	 * or no further upgrade path exists, then stamps the resulting version.
	 *
	 * @param array<string, mixed> $config Configuration array.
	 * @param int                  $target Target schema version.
	 * @return array<string, mixed>
	 */
	public function upgrade_to( array $config, int $target ): array {
		$version = $this->version_of( $config );

		while ( $version < $target ) {
			if ( ! isset( $this->upgraders[ $version ] ) ) {
				break;
			}

			$config = ( $this->upgraders[ $version ] )( $config );
			++$version;
		}

		$config['schema_version'] = max( $version, self::FIRST_VERSION );

		return $config;
	}
}
