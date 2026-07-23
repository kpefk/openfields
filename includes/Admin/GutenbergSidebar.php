<?php
/**
 * Gutenberg sidebar integration.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Admin;

use OpenFields\Core\Assets;
use OpenFields\Core\PostType;
use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\GroupMatcher;

defined( 'ABSPATH' ) || exit;

/**
 * In the block editor, enqueues the admin bundle and passes the matching field
 * groups to the client, which renders them in a document settings sidebar panel
 * bound to post meta via `@wordpress/core-data`.
 */
final class GutenbergSidebar {

	/**
	 * Group matcher.
	 *
	 * @var GroupMatcher
	 */
	private GroupMatcher $matcher;

	/**
	 * Asset enqueuer.
	 *
	 * @var Assets
	 */
	private Assets $assets;

	/**
	 * Build the sidebar integration with its collaborators.
	 *
	 * @param GroupMatcher $matcher Group matcher.
	 * @param Assets       $assets  Asset enqueuer.
	 */
	public function __construct( GroupMatcher $matcher, Assets $assets ) {
		$this->matcher = $matcher;
		$this->assets  = $assets;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the bundle and localise the matching groups for the current post.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		$post = get_post();

		if ( ! $post instanceof \WP_Post || PostType::POST_TYPE === $post->post_type ) {
			return;
		}

		$groups = $this->matcher->for_post( $post );

		if ( array() === $groups ) {
			return;
		}

		$this->assets->enqueue_app();

		$data = array_map(
			static fn ( FieldGroup $group ): array => $group->to_array(),
			$groups
		);

		wp_add_inline_script(
			'openfields-index',
			'window.openfieldsEditor = ' . wp_json_encode( array( 'groups' => $data ) ) . ';',
			'before'
		);
	}
}
