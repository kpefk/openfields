<?php
/**
 * WordPress function stubs in the OpenFields\Core namespace.
 *
 * Unqualified calls to these functions inside classes under test (which live in
 * OpenFields\Core) resolve here first, before the global namespace. Each stub
 * delegates to {@see \OpenFields\Tests\WpStubs}, letting tests set return values
 * and assert on the arguments. Pure pass-through helpers (i18n/escaping) return
 * their input directly.
 *
 * Loaded via tests/bootstrap.php.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Core;

use OpenFields\Tests\WpStubs;

if ( ! function_exists( __NAMESPACE__ . '\\wp_create_nonce' ) ) {

	/**
	 * @param string $action Action name.
	 * @return mixed
	 */
	function wp_create_nonce( string $action ) {
		return WpStubs::invoke( 'wp_create_nonce', func_get_args() );
	}

	/**
	 * @param string $nonce  Nonce value.
	 * @param string $action Action name.
	 * @return mixed
	 */
	function wp_verify_nonce( string $nonce, string $action ) {
		return WpStubs::invoke( 'wp_verify_nonce', func_get_args() );
	}

	/**
	 * @param string $capability Capability.
	 * @return mixed
	 */
	function current_user_can( string $capability ) {
		return WpStubs::invoke( 'current_user_can', func_get_args() );
	}

	/**
	 * @param int    $user_id    User ID.
	 * @param string $capability Capability.
	 * @return mixed
	 */
	function user_can( int $user_id, string $capability ) {
		return WpStubs::invoke( 'user_can', func_get_args() );
	}

	/**
	 * @param string               $post_type Post type key.
	 * @param array<string, mixed> $args      Arguments.
	 * @return mixed
	 */
	function register_post_type( string $post_type, array $args ) {
		return WpStubs::invoke( 'register_post_type', func_get_args() );
	}

	/**
	 * @param string               $status Status key.
	 * @param array<string, mixed> $args   Arguments.
	 * @return mixed
	 */
	function register_post_status( string $status, array $args ) {
		return WpStubs::invoke( 'register_post_status', func_get_args() );
	}

	/**
	 * @param string               $object_type Object type.
	 * @param string               $meta_key    Meta key.
	 * @param array<string, mixed> $args        Arguments.
	 * @return mixed
	 */
	function register_meta( string $object_type, string $meta_key, array $args ) {
		return WpStubs::invoke( 'register_meta', func_get_args() );
	}

	/**
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __( string $text, string $domain = 'default' ): string {
		return $text;
	}

	/**
	 * @param string $text    Text.
	 * @param string $context Context.
	 * @param string $domain  Text domain.
	 * @return string
	 */
	function _x( string $text, string $context, string $domain = 'default' ): string {
		return $text;
	}

	/**
	 * @param string      $singular Singular form.
	 * @param string      $plural   Plural form.
	 * @param string|null $domain   Text domain.
	 * @return array<string, mixed>
	 */
	function _n_noop( string $singular, string $plural, ?string $domain = null ): array {
		return array(
			0          => $singular,
			1          => $plural,
			'singular' => $singular,
			'plural'   => $plural,
			'domain'   => $domain,
		);
	}

	/**
	 * @param string $text Text.
	 * @return string
	 */
	function esc_html( string $text ): string {
		return $text;
	}
}
