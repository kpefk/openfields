<?php
/**
 * In-memory registry of programmatically registered field groups.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

defined( 'ABSPATH' ) || exit;

/**
 * Holds field groups registered in code (via
 * {@see openfields_add_local_field_group()} or Local JSON), so the repository
 * can surface them alongside database-stored groups.
 */
final class LocalStore {

	/**
	 * Registered group configurations keyed by group key.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $groups = array();

	/**
	 * Register a field group from a configuration array.
	 *
	 * @param array<string, mixed> $config Group configuration.
	 * @return void
	 */
	public function add( array $config ): void {
		$group = FieldGroup::from_array( $config );

		$this->groups[ $group->key() ] = $group->to_array();
	}

	/**
	 * Whether a group key is registered locally.
	 *
	 * @param string $key Group key.
	 * @return bool
	 */
	public function has( string $key ): bool {
		return isset( $this->groups[ $key ] );
	}

	/**
	 * All locally registered field groups.
	 *
	 * @return FieldGroup[]
	 */
	public function all(): array {
		return array_map(
			static fn ( array $config ): FieldGroup => FieldGroup::from_array( $config ),
			array_values( $this->groups )
		);
	}
}
