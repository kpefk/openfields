<?php
/**
 * WordPress function/class stubs for unit tests.
 *
 * Classes under test call WordPress functions unqualified, so PHP resolves them
 * in the class's own namespace before falling back to the global one. We define
 * the needed functions in each namespace, delegating to
 * {@see \OpenFields\Tests\WpStubs} so tests can set return values and assert on
 * arguments. Pure pass-through helpers (i18n/escaping) return their input.
 *
 * Loaded via tests/bootstrap.php.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Core {

	use OpenFields\Tests\WpStubs;

	if ( ! function_exists( __NAMESPACE__ . '\\wp_create_nonce' ) ) {
		function wp_create_nonce( string $action ) {
			return WpStubs::invoke( 'wp_create_nonce', func_get_args() );
		}
		function wp_verify_nonce( string $nonce, string $action ) {
			return WpStubs::invoke( 'wp_verify_nonce', func_get_args() );
		}
		function current_user_can( string $capability ) {
			return WpStubs::invoke( 'current_user_can', func_get_args() );
		}
		function user_can( int $user_id, string $capability ) {
			return WpStubs::invoke( 'user_can', func_get_args() );
		}
		function register_post_type( string $post_type, array $args ) {
			return WpStubs::invoke( 'register_post_type', func_get_args() );
		}
		function register_post_status( string $status, array $args ) {
			return WpStubs::invoke( 'register_post_status', func_get_args() );
		}
		function register_meta( string $object_type, string $meta_key, array $args ) {
			return WpStubs::invoke( 'register_meta', func_get_args() );
		}
		function __( string $text, string $domain = 'default' ): string {
			return $text;
		}
		function _x( string $text, string $context, string $domain = 'default' ): string {
			return $text;
		}
		function _n_noop( string $singular, string $plural, ?string $domain = null ): array {
			return array( $singular, $plural );
		}
		function esc_html( string $text ): string {
			return $text;
		}
	}
}

namespace OpenFields\FieldGroups {

	use OpenFields\Tests\WpStubs;

	if ( ! function_exists( __NAMESPACE__ . '\\get_post' ) ) {
		function get_post( $post = null ) {
			return WpStubs::invoke( 'get_post', func_get_args() );
		}
		function wp_json_encode( $data, int $options = 0, int $depth = 512 ) {
			return \json_encode( $data, $options, $depth );
		}
		function apply_filters( string $hook, $value, ...$args ) {
			return $value;
		}
		function wp_cache_get( $key, string $group = '', bool $force = false, &$found = null ) {
			return WpStubs::invoke( 'wp_cache_get', array( $key, $group ) );
		}
		function wp_cache_set( $key, $value, string $group = '', int $expire = 0 ) {
			return WpStubs::invoke( 'wp_cache_set', array( $key, $value, $group ) );
		}
	}
}

namespace OpenFields\Support {

	if ( ! function_exists( __NAMESPACE__ . '\\sanitize_text_field' ) ) {
		function sanitize_text_field( string $value ): string {
			return trim( (string) preg_replace( '/[\r\n\t ]+/', ' ', wp_strip_all_tags_shim( $value ) ) );
		}
		function sanitize_key( string $key ): string {
			return (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $key ) );
		}
		/**
		 * Minimal tag stripper for the sanitize_text_field stub.
		 *
		 * @param string $value Raw value.
		 * @return string
		 */
		function wp_strip_all_tags_shim( string $value ): string {
			return (string) preg_replace( '/<[^>]*>/', '', $value );
		}
	}
}

namespace {

	if ( ! class_exists( 'WP_Post' ) ) {
		/**
		 * Minimal WP_Post stand-in for unit tests.
		 */
		#[\AllowDynamicProperties]
		class WP_Post {
			public int $ID = 0;
			public string $post_type = '';
			public string $post_content = '';
			public string $post_title = '';
			public string $post_status = 'publish';

			/**
			 * @param array<string, mixed> $props Property overrides.
			 */
			public function __construct( array $props = array() ) {
				foreach ( $props as $key => $value ) {
					$this->{$key} = $value;
				}
			}
		}
	}
}
