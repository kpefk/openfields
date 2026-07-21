<?php
/**
 * Centralized security helpers (nonces and capabilities).
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Single source of truth for nonce creation/verification and capability checks.
 *
 * All plugin forms and REST endpoints route through these helpers so nonce
 * action names and capability names are consistent across the codebase.
 */
final class Security {

	/**
	 * Capability required to manage field groups.
	 *
	 * @var string
	 */
	public const CAP_MANAGE_FIELD_GROUPS = 'edit_field_groups';

	/**
	 * Capability required to manage options pages.
	 *
	 * @var string
	 */
	public const CAP_MANAGE_OPTIONS_PAGES = 'manage_options_pages';

	/**
	 * Create a nonce for a namespaced action.
	 *
	 * @param string $action Bare action name (namespaced internally).
	 * @return string
	 */
	public function create_nonce( string $action ): string {
		return wp_create_nonce( $this->namespaced_action( $action ) );
	}

	/**
	 * Verify a nonce for a namespaced action.
	 *
	 * @param string $nonce  Nonce value to verify.
	 * @param string $action Bare action name (namespaced internally).
	 * @return bool
	 */
	public function verify_nonce( string $nonce, string $action ): bool {
		return false !== wp_verify_nonce( $nonce, $this->namespaced_action( $action ) );
	}

	/**
	 * Determine whether a user can manage field groups.
	 *
	 * @param int|null $user_id Optional user ID; defaults to the current user.
	 * @return bool
	 */
	public function can_manage_field_groups( ?int $user_id = null ): bool {
		if ( null === $user_id ) {
			return current_user_can( self::CAP_MANAGE_FIELD_GROUPS );
		}

		return user_can( $user_id, self::CAP_MANAGE_FIELD_GROUPS );
	}

	/**
	 * Determine whether a user can manage options pages.
	 *
	 * @param int|null $user_id Optional user ID; defaults to the current user.
	 * @return bool
	 */
	public function can_manage_options_pages( ?int $user_id = null ): bool {
		if ( null === $user_id ) {
			return current_user_can( self::CAP_MANAGE_OPTIONS_PAGES );
		}

		return user_can( $user_id, self::CAP_MANAGE_OPTIONS_PAGES );
	}

	/**
	 * Namespace a bare action/nonce name under the plugin prefix.
	 *
	 * @param string $action Bare action name.
	 * @return string
	 */
	private function namespaced_action( string $action ): string {
		return 'openfields_' . $action;
	}
}
