<?php
/**
 * Uninstall handler for OpenFields.
 *
 * Removes all plugin data on deletion: field-group posts, their meta, options
 * and custom capabilities. On multisite the cleanup runs per-site.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Uninstall;

// Exit if not called by WordPress during uninstall.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Remove all OpenFields data from the current site.
 *
 * @return void
 */
function purge_site(): void {
	$group_posts = get_posts(
		array(
			'post_type'      => 'openfields-group',
			'post_status'    => 'any',
			'numberposts'    => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'suppress_filters' => true,
		)
	);

	foreach ( $group_posts as $post_id ) {
		wp_delete_post( (int) $post_id, true );
	}

	// Plugin-owned options (prefixed) are removed here as they are introduced.
	delete_option( 'openfields_version' );
	delete_option( 'openfields_settings' );

	// Custom capabilities.
	$roles = wp_roles();

	foreach ( $roles->role_objects as $role ) {
		$role->remove_cap( 'edit_field_groups' );
		$role->remove_cap( 'manage_options_pages' );
	}
}

if ( is_multisite() ) {
	$site_ids = get_sites( array( 'fields' => 'ids' ) );

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		purge_site();
		restore_current_blog();
	}
} else {
	purge_site();
}
