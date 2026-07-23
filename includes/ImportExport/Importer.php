<?php
/**
 * Field-group importer.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\ImportExport;

use OpenFields\Core\PostType;
use OpenFields\FieldGroups\FieldGroup;

defined( 'ABSPATH' ) || exit;

/**
 * Imports field groups from JSON, creating or updating `openfields-group` posts.
 * Existing groups are matched by key so re-imports update in place. Each
 * configuration is normalised and upgraded via {@see FieldGroup::from_array()}.
 */
final class Importer {

	/**
	 * Import field groups from a JSON string.
	 *
	 * @param string $json JSON: a single group or a list of groups.
	 * @return string[] Keys of the imported groups.
	 */
	public function from_json( string $json ): array {
		$decoded = json_decode( $json, true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$imported = array();

		foreach ( $this->normalize( $decoded ) as $config ) {
			if ( ! is_array( $config ) ) {
				continue;
			}

			$group = FieldGroup::from_array( $config );
			$this->save( $group );
			$imported[] = $group->key();
		}

		return $imported;
	}

	/**
	 * Normalise decoded JSON into a list of group configurations.
	 *
	 * @param array<int|string, mixed> $decoded Decoded JSON.
	 * @return array<int, mixed>
	 */
	private function normalize( array $decoded ): array {
		if ( isset( $decoded['key'] ) || isset( $decoded['fields'] ) || isset( $decoded['title'] ) ) {
			return array( $decoded );
		}

		return array_values( $decoded );
	}

	/**
	 * Create or update the `openfields-group` post for a group.
	 *
	 * @param FieldGroup $group Field group.
	 * @return void
	 */
	private function save( FieldGroup $group ): void {
		$postarr = array(
			'post_type'    => PostType::POST_TYPE,
			'post_status'  => $group->is_active() ? 'publish' : PostType::STATUS_DISABLED,
			'post_title'   => $group->title(),
			'post_content' => $group->to_json(),
		);

		$existing = $this->find_post_id( $group->key() );

		if ( null !== $existing ) {
			$postarr['ID'] = $existing;
			wp_update_post( $postarr );

			return;
		}

		wp_insert_post( $postarr );
	}

	/**
	 * Find the post ID of an existing group by its key.
	 *
	 * @param string $key Group key.
	 * @return int|null
	 */
	private function find_post_id( string $key ): ?int {
		$posts = get_posts(
			array(
				'post_type'   => PostType::POST_TYPE,
				'post_status' => array( 'publish', PostType::STATUS_DISABLED ),
				'numberposts' => -1,
			)
		);

		foreach ( $posts as $post ) {
			$group = FieldGroup::from_post( $post );

			if ( null !== $group && $group->key() === $key ) {
				return (int) $post->ID;
			}
		}

		return null;
	}
}
