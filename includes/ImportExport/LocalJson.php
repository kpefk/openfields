<?php
/**
 * Local JSON sync.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\ImportExport;

use OpenFields\FieldGroups\LocalStore;

defined( 'ABSPATH' ) || exit;

/**
 * Loads field groups from `openfields-json/` directories in the active theme (or
 * paths added via the `openfields/local_json_paths` filter) into the local
 * store. Because the repository lets locally-registered groups override
 * database-stored ones by key, Local JSON is the source of truth for a group's
 * *configuration* — field values remain in post meta.
 *
 * Each `*.json` file holds a single group configuration.
 */
final class LocalJson {

	/**
	 * Local group store.
	 *
	 * @var LocalStore
	 */
	private LocalStore $store;

	/**
	 * Build with the local store.
	 *
	 * @param LocalStore $store Local group store.
	 */
	public function __construct( LocalStore $store ) {
		$this->store = $store;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'load' ), 5 );
	}

	/**
	 * Load every configured Local JSON directory.
	 *
	 * @return void
	 */
	public function load(): void {
		foreach ( $this->directories() as $directory ) {
			$this->load_directory( $directory );
		}
	}

	/**
	 * Load all group JSON files from a directory into the local store.
	 *
	 * @param string $directory Directory path.
	 * @return void
	 */
	public function load_directory( string $directory ): void {
		if ( ! is_dir( $directory ) ) {
			return;
		}

		$files = glob( rtrim( $directory, '/\\' ) . '/*.json' );

		if ( ! is_array( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a bundled local config file.
			$json = file_get_contents( $file );

			if ( false === $json ) {
				continue;
			}

			$config = json_decode( $json, true );

			if ( is_array( $config ) ) {
				$this->store->add( $config );
			}
		}
	}

	/**
	 * The directories to scan for Local JSON.
	 *
	 * @return string[]
	 */
	private function directories(): array {
		$directories = array(
			get_stylesheet_directory() . '/openfields-json',
			get_template_directory() . '/openfields-json',
		);

		/**
		 * Filters the directories scanned for Local JSON field groups.
		 *
		 * @since 0.1.0
		 *
		 * @param string[] $directories Absolute directory paths.
		 */
		$directories = apply_filters( 'openfields/local_json_paths', $directories );

		return array_values( array_unique( $directories ) );
	}
}
