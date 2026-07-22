<?php
/**
 * WordPress function/class stubs for unit tests.
 *
 * These are defined in the global namespace. Classes under test call WordPress
 * functions unqualified, so PHP resolves them in the class's own namespace and
 * then falls back to the global namespace — where these stubs live. Behaviour-
 * sensitive functions delegate to {@see \OpenFields\Tests\WpStubs} so tests can
 * set return values and assert on arguments; pure helpers implement a realistic
 * pass-through.
 *
 * Loaded via tests/bootstrap.php.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

use OpenFields\Tests\WpStubs;

/*
 * ---------------------------------------------------------------------------
 * Behaviour-sensitive functions (delegate to the WpStubs registry).
 * ---------------------------------------------------------------------------
 */

if ( ! function_exists( 'wp_create_nonce' ) ) {
	function wp_create_nonce( $action = -1 ) {
		return WpStubs::invoke( 'wp_create_nonce', func_get_args() );
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action = -1 ) {
		return WpStubs::invoke( 'wp_verify_nonce', func_get_args() );
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability, ...$args ) {
		return WpStubs::invoke( 'current_user_can', array( $capability ) );
	}
}

if ( ! function_exists( 'user_can' ) ) {
	function user_can( $user, $capability, ...$args ) {
		return WpStubs::invoke( 'user_can', array( $user, $capability ) );
	}
}

if ( ! function_exists( 'register_post_type' ) ) {
	function register_post_type( $post_type, $args = array() ) {
		return WpStubs::invoke( 'register_post_type', array( $post_type, $args ) );
	}
}

if ( ! function_exists( 'register_post_status' ) ) {
	function register_post_status( $status, $args = array() ) {
		return WpStubs::invoke( 'register_post_status', array( $status, $args ) );
	}
}

if ( ! function_exists( 'register_meta' ) ) {
	function register_meta( $object_type, $meta_key, $args = array() ) {
		return WpStubs::invoke( 'register_meta', array( $object_type, $meta_key, $args ) );
	}
}

if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post = null ) {
		return WpStubs::invoke( 'get_post', func_get_args() );
	}
}

if ( ! function_exists( 'get_posts' ) ) {
	function get_posts( $args = array() ) {
		$result = WpStubs::invoke( 'get_posts', array( $args ) );

		return is_array( $result ) ? $result : array();
	}
}

if ( ! function_exists( 'update_post_meta' ) ) {
	function update_post_meta( $post_id, $meta_key, $meta_value, $prev = '' ) {
		return WpStubs::invoke( 'update_post_meta', array( $post_id, $meta_key, $meta_value ) );
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $meta_key = '', $single = false ) {
		return WpStubs::invoke( 'get_post_meta', array( $post_id, $meta_key, $single ) );
	}
}

if ( ! function_exists( 'wp_cache_get' ) ) {
	function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
		return WpStubs::invoke( 'wp_cache_get', array( $key, $group ) );
	}
}

if ( ! function_exists( 'wp_cache_set' ) ) {
	function wp_cache_set( $key, $value, $group = '', $expire = 0 ) {
		return WpStubs::invoke( 'wp_cache_set', array( $key, $value, $group ) );
	}
}

/*
 * ---------------------------------------------------------------------------
 * Pure pass-through helpers (deterministic, no handler required).
 * ---------------------------------------------------------------------------
 */

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( '_x' ) ) {
	function _x( $text, $context, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( '_n_noop' ) ) {
	function _n_noop( $singular, $plural, $domain = null ) {
		return array( $singular, $plural );
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return (string) $text;
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return trim( (string) $url );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		$value = (string) preg_replace( '/<[^>]*>/', '', (string) $value );

		return trim( (string) preg_replace( '/[\r\n\t ]+/', ' ', $value ) );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $value ) {
		$value = (string) preg_replace( '/<[^>]*>/', '', (string) $value );

		return trim( $value );
	}
}

if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $email ) {
		$filtered = filter_var( trim( (string) $email ), FILTER_SANITIZE_EMAIL );

		return false === $filtered ? '' : $filtered;
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
	}
}

if ( ! function_exists( 'is_email' ) ) {
	function is_email( $email ) {
		$valid = filter_var( (string) $email, FILTER_VALIDATE_EMAIL );

		return false === $valid ? false : $valid;
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $value ) {
		return (string) preg_replace( '#<script\b[^>]*>.*?</script>#is', '', (string) $value );
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, (int) $options, (int) $depth );
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value = null, ...$args ) {
		return $value;
	}
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $hook, ...$args ) {
		WpStubs::invoke( 'do_action', array_merge( array( $hook ), $args ) );
	}
}

/*
 * ---------------------------------------------------------------------------
 * Minimal class stand-ins.
 * ---------------------------------------------------------------------------
 */

if ( ! class_exists( 'WP_Error' ) ) {
	/**
	 * Minimal WP_Error stand-in for unit tests.
	 */
	class WP_Error {

		/**
		 * Error messages keyed by code.
		 *
		 * @var array<string, string[]>
		 */
		public array $errors = array();

		/**
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 * @param mixed  $data    Optional error data.
		 */
		public function __construct( $code = '', $message = '', $data = '' ) {
			if ( '' !== $code ) {
				$this->errors[ $code ][] = $message;
			}
		}

		/**
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 * @param mixed  $data    Optional error data.
		 * @return void
		 */
		public function add( $code, $message = '', $data = '' ) {
			$this->errors[ $code ][] = $message;
		}

		/**
		 * @return string
		 */
		public function get_error_code() {
			$codes = array_keys( $this->errors );

			return $codes[0] ?? '';
		}

		/**
		 * @return string[]
		 */
		public function get_error_codes() {
			return array_keys( $this->errors );
		}

		/**
		 * @param string $code Optional code.
		 * @return string
		 */
		public function get_error_message( $code = '' ) {
			if ( '' === $code ) {
				$code = $this->get_error_code();
			}

			return $this->errors[ $code ][0] ?? '';
		}

		/**
		 * @return bool
		 */
		public function has_errors() {
			return array() !== $this->errors;
		}
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof \WP_Error;
	}
}

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
