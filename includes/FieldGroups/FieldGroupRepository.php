<?php
/**
 * Repository for field-group posts.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

use OpenFields\Core\PostType;

defined( 'ABSPATH' ) || exit;

/**
 * Loads {@see FieldGroup} models from `openfields-group` posts.
 */
final class FieldGroupRepository {

	/**
	 * Schema upgrader applied when loading each group.
	 *
	 * @var SchemaUpgrader
	 */
	private SchemaUpgrader $upgrader;

	/**
	 * Store of programmatically registered groups.
	 *
	 * @var LocalStore
	 */
	private LocalStore $local;

	/**
	 * Build the repository with an optional schema upgrader and local store.
	 *
	 * @param SchemaUpgrader|null $upgrader Optional upgrader.
	 * @param LocalStore|null     $local    Optional local group store.
	 */
	public function __construct( ?SchemaUpgrader $upgrader = null, ?LocalStore $local = null ) {
		$this->upgrader = $upgrader ?? new SchemaUpgrader();
		$this->local    = $local ?? new LocalStore();
	}

	/**
	 * Load all field groups.
	 *
	 * @param bool $include_disabled Whether to include disabled groups.
	 * @return FieldGroup[]
	 */
	public function all( bool $include_disabled = false ): array {
		$statuses = $include_disabled
			? array( 'publish', PostType::STATUS_DISABLED )
			: array( 'publish' );

		$posts = get_posts(
			array(
				'post_type'   => PostType::POST_TYPE,
				'post_status' => $statuses,
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		$groups = array();

		foreach ( $posts as $post ) {
			$group = FieldGroup::from_post( $post, $this->upgrader );

			if ( null !== $group ) {
				$groups[] = $group;
			}
		}

		foreach ( $this->local->all() as $group ) {
			$groups[] = $group;
		}

		return $groups;
	}

	/**
	 * Load only active (published, non-disabled) field groups.
	 *
	 * @return FieldGroup[]
	 */
	public function active(): array {
		return array_values(
			array_filter(
				$this->all(),
				static fn ( FieldGroup $group ): bool => $group->is_active()
			)
		);
	}

	/**
	 * Find a field group by its key.
	 *
	 * @param string $key Group key.
	 * @return FieldGroup|null
	 */
	public function find( string $key ): ?FieldGroup {
		foreach ( $this->all( true ) as $group ) {
			if ( $group->key() === $key ) {
				return $group;
			}
		}

		return null;
	}
}
