<?php
/**
 * Registration of the field-group custom post type.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the `openfields-group` custom post type and its custom statuses.
 *
 * Field groups are stored as posts of this type; their configuration lives in
 * the post content as JSON. Access is gated behind the
 * {@see Security::CAP_MANAGE_FIELD_GROUPS} capability.
 */
final class PostType {

	/**
	 * Post type key.
	 *
	 * @var string
	 */
	public const POST_TYPE = 'openfields-group';

	/**
	 * Custom "disabled" post status key.
	 *
	 * @var string
	 */
	public const STATUS_DISABLED = 'openfields_disabled';

	/**
	 * Register the custom post type.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type( self::POST_TYPE, $this->post_type_args() );
	}

	/**
	 * Register the custom "disabled" post status.
	 *
	 * @return void
	 */
	public function register_status(): void {
		register_post_status(
			self::STATUS_DISABLED,
			array(
				'label'                     => _x( 'Disabled', 'field group status', 'openfields' ),
				'public'                    => false,
				'internal'                  => true,
				'protected'                 => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of disabled field groups. */
				'label_count'               => _n_noop(
					'Disabled <span class="count">(%s)</span>',
					'Disabled <span class="count">(%s)</span>',
					'openfields'
				),
			)
		);
	}

	/**
	 * Build the arguments for {@see register_post_type()}.
	 *
	 * @return array<string, mixed>
	 */
	private function post_type_args(): array {
		$cap = Security::CAP_MANAGE_FIELD_GROUPS;

		return array(
			'labels'              => $this->labels(),
			'description'         => __( 'OpenFields field groups.', 'openfields' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-feedback',
			'menu_position'       => 80,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'can_export'          => true,
			'delete_with_user'    => false,
			'exclude_from_search' => true,
			'map_meta_cap'        => true,
			'capabilities'        => array(
				'edit_post'          => $cap,
				'read_post'          => $cap,
				'delete_post'        => $cap,
				'edit_posts'         => $cap,
				'edit_others_posts'  => $cap,
				'delete_posts'       => $cap,
				'publish_posts'      => $cap,
				'read_private_posts' => $cap,
				'create_posts'       => $cap,
			),
		);
	}

	/**
	 * Build the post type labels.
	 *
	 * @return array<string, string>
	 */
	private function labels(): array {
		return array(
			'name'               => _x( 'Field Groups', 'post type general name', 'openfields' ),
			'singular_name'      => _x( 'Field Group', 'post type singular name', 'openfields' ),
			'menu_name'          => _x( 'Field Groups', 'admin menu', 'openfields' ),
			'add_new'            => __( 'Add New', 'openfields' ),
			'add_new_item'       => __( 'Add New Field Group', 'openfields' ),
			'edit_item'          => __( 'Edit Field Group', 'openfields' ),
			'new_item'           => __( 'New Field Group', 'openfields' ),
			'view_item'          => __( 'View Field Group', 'openfields' ),
			'search_items'       => __( 'Search Field Groups', 'openfields' ),
			'not_found'          => __( 'No field groups found.', 'openfields' ),
			'not_found_in_trash' => __( 'No field groups found in Trash.', 'openfields' ),
			'all_items'          => __( 'Field Groups', 'openfields' ),
		);
	}
}
