<?php
/**
 * Activation and deactivation routines.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin activation and deactivation.
 *
 * Activation grants plugin capabilities to the administrator role and records
 * the installed version. Data cleanup happens on uninstall, not deactivation.
 */
final class Activation {

	/**
	 * Capabilities granted to the administrator role on activation.
	 *
	 * @var string[]
	 */
	private const CAPABILITIES = array(
		Security::CAP_MANAGE_FIELD_GROUPS,
		Security::CAP_MANAGE_OPTIONS_PAGES,
	);

	/**
	 * Run activation tasks.
	 *
	 * @return void
	 */
	public static function activate(): void {
		self::add_capabilities();

		update_option( 'openfields_version', OPENFIELDS_VERSION );
	}

	/**
	 * Run deactivation tasks.
	 *
	 * Capabilities and data are intentionally preserved; full cleanup happens
	 * on uninstall.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	/**
	 * Grant plugin capabilities to the administrator role.
	 *
	 * @return void
	 */
	private static function add_capabilities(): void {
		$role = get_role( 'administrator' );

		if ( ! $role instanceof \WP_Role ) {
			return;
		}

		foreach ( self::CAPABILITIES as $capability ) {
			$role->add_cap( $capability );
		}
	}
}
