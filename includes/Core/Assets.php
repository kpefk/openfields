<?php
/**
 * Admin asset enqueuing.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues compiled admin assets built by @wordpress/scripts.
 *
 * Each entry ships a `*.asset.php` manifest describing its script dependencies
 * and a content hash version, which is used when registering the script.
 */
final class Assets {

	/**
	 * Enqueue admin assets for the current screen.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 * @return void
	 */
	public function enqueue( string $hook_suffix ): void {
		unset( $hook_suffix );

		if ( ! $this->is_field_group_screen() ) {
			return;
		}

		$this->enqueue_app();
	}

	/**
	 * Enqueue the admin application bundle. Idempotent (safe to call repeatedly).
	 *
	 * @return void
	 */
	public function enqueue_app(): void {
		$this->enqueue_entry( 'index' );
	}

	/**
	 * Whether the current screen is the field-group editor.
	 *
	 * @return bool
	 */
	private function is_field_group_screen(): bool {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		return $screen instanceof \WP_Screen && PostType::POST_TYPE === $screen->post_type;
	}

	/**
	 * Enqueue a compiled build entry by name.
	 *
	 * @param string $entry Entry name (without extension).
	 * @return void
	 */
	private function enqueue_entry( string $entry ): void {
		$script_rel = 'assets/build/' . $entry . '.js';
		$script_abs = OPENFIELDS_PATH . $script_rel;

		if ( ! file_exists( $script_abs ) ) {
			return;
		}

		$asset = $this->asset_manifest( $entry );

		wp_enqueue_script(
			'openfields-' . $entry,
			OPENFIELDS_URL . $script_rel,
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}

	/**
	 * Read the build manifest for an entry, falling back to sensible defaults.
	 *
	 * @param string $entry Entry name (without extension).
	 * @return array{dependencies: string[], version: string}
	 */
	private function asset_manifest( string $entry ): array {
		$manifest_path = OPENFIELDS_PATH . 'assets/build/' . $entry . '.asset.php';

		$dependencies = array();
		$version      = OPENFIELDS_VERSION;

		if ( file_exists( $manifest_path ) ) {
			$manifest = require $manifest_path;

			if ( is_array( $manifest ) ) {
				$dependencies = isset( $manifest['dependencies'] ) && is_array( $manifest['dependencies'] )
					? $manifest['dependencies']
					: array();
				$version      = isset( $manifest['version'] ) ? (string) $manifest['version'] : OPENFIELDS_VERSION;
			}
		}

		return array(
			'dependencies' => $dependencies,
			'version'      => $version,
		);
	}
}
