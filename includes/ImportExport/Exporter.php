<?php
/**
 * Field-group exporter.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\ImportExport;

use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\FieldGroupRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Exports field groups as JSON or as generated PHP registration code.
 */
final class Exporter {

	/**
	 * Field group repository.
	 *
	 * @var FieldGroupRepository
	 */
	private FieldGroupRepository $repository;

	/**
	 * Build the exporter with its repository.
	 *
	 * @param FieldGroupRepository $repository Field group repository.
	 */
	public function __construct( FieldGroupRepository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Export groups (by key, or all when empty) as pretty-printed JSON.
	 *
	 * @param string[] $keys Group keys to export; empty exports all.
	 * @return string
	 */
	public function to_json( array $keys = array() ): string {
		$data = array_map(
			static fn ( FieldGroup $group ): array => $group->to_array(),
			$this->groups_for( $keys )
		);

		return (string) wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	/**
	 * Export groups as PHP code that registers them via the public API.
	 *
	 * @param string[] $keys Group keys to export; empty exports all.
	 * @return string
	 */
	public function to_php( array $keys = array() ): string {
		$code = "<?php\n\n";

		foreach ( $this->groups_for( $keys ) as $group ) {
			$code .= 'openfields_add_local_field_group( '
				. var_export( $group->to_array(), true ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- Generating exportable PHP code, not debug output.
				. " );\n\n";
		}

		return $code;
	}

	/**
	 * Resolve the groups to export.
	 *
	 * @param string[] $keys Group keys; empty means all.
	 * @return FieldGroup[]
	 */
	private function groups_for( array $keys ): array {
		$groups = $this->repository->all( true );

		if ( array() === $keys ) {
			return $groups;
		}

		return array_values(
			array_filter(
				$groups,
				static fn ( FieldGroup $group ): bool => in_array( $group->key(), $keys, true )
			)
		);
	}
}
