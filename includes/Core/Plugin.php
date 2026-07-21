<?php
/**
 * Core plugin bootstrap.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin orchestrator.
 *
 * Milestone 0 provides a minimal, safe entry point. Subsystem wiring (CPT,
 * field types, REST, admin) is introduced in later milestones through a
 * dependency-injection container.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Whether boot() has already run.
	 *
	 * @var bool
	 */
	private bool $booted = false;

	/**
	 * Private constructor — use {@see Plugin::instance()}.
	 */
	private function __construct() {}

	/**
	 * Retrieve the shared instance.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the plugin. Idempotent.
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		add_action( 'init', array( $this, 'load_textdomain' ) );

		/**
		 * Fires after OpenFields has booted.
		 *
		 * @since 0.1.0
		 */
		do_action( 'openfields/booted' );
	}

	/**
	 * Load the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'openfields',
			false,
			dirname( plugin_basename( OPENFIELDS_FILE ) ) . '/languages'
		);
	}
}
