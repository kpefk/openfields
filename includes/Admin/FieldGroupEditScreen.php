<?php
/**
 * Field-group edit screen: renders the builder and persists its output.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Admin;

use OpenFields\Core\PostType;
use OpenFields\Core\Security;
use OpenFields\FieldGroups\FieldGroup;
use OpenFields\Support\Sanitizer;

defined( 'ABSPATH' ) || exit;

/**
 * Mounts the React Field Group Builder on the `openfields-group` edit screen and
 * saves its serialized configuration into the post content.
 */
final class FieldGroupEditScreen {

	/**
	 * Nonce action for saving a field group.
	 *
	 * @var string
	 */
	private const NONCE_ACTION = 'save_field_group';

	/**
	 * Nonce request field name.
	 *
	 * @var string
	 */
	private const NONCE_FIELD = 'openfields_field_group_nonce';

	/**
	 * Request field carrying the serialized builder configuration.
	 *
	 * @var string
	 */
	private const DATA_FIELD = 'openfields_field_group_data';

	/**
	 * Security helper.
	 *
	 * @var Security
	 */
	private Security $security;

	/**
	 * Build the edit screen with its security helper.
	 *
	 * @param Security $security Security helper.
	 */
	public function __construct( Security $security ) {
		$this->security = $security;
	}

	/**
	 * Register the screen hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'edit_form_after_title', array( $this, 'render' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'inject_config' ), 10, 2 );
	}

	/**
	 * Render the builder container, hidden input and nonce after the title.
	 *
	 * @param \WP_Post $post Current post.
	 * @return void
	 */
	public function render( \WP_Post $post ): void {
		if ( PostType::POST_TYPE !== $post->post_type ) {
			return;
		}

		$group  = FieldGroup::from_post( $post );
		$config = null !== $group ? $group->to_array() : array();

		printf(
			'<input type="hidden" name="%1$s" value="%2$s" />',
			esc_attr( self::NONCE_FIELD ),
			esc_attr( $this->security->create_nonce( self::NONCE_ACTION ) )
		);
		printf(
			'<input type="hidden" id="openfields-field-group-data" name="%1$s" value="" />',
			esc_attr( self::DATA_FIELD )
		);
		printf(
			'<div id="openfields-field-group-builder" data-config="%1$s"></div>',
			esc_attr( (string) wp_json_encode( $config ) )
		);
	}

	/**
	 * Inject the sanitized builder configuration into the post content on save.
	 *
	 * Uses the `wp_insert_post_data` filter (runs before the DB write) so there
	 * is no save_post recursion.
	 *
	 * @param array<string, mixed> $data    Slashed post data about to be saved.
	 * @param array<string, mixed> $postarr Raw post array.
	 * @return array<string, mixed>
	 */
	public function inject_config( array $data, array $postarr ): array {
		if (
			! isset( $data['post_type'] ) ||
			PostType::POST_TYPE !== $data['post_type']
		) {
			return $data;
		}

		// WPCS cannot see nonce verification through Security::verify_nonce(); it
		// is performed explicitly below before the payload is used.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST[ self::DATA_FIELD ], $_POST[ self::NONCE_FIELD ] ) ) {
			return $data;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) );

		if ( ! $this->security->verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return $data;
		}

		if ( ! $this->security->can_manage_field_groups() ) {
			return $data;
		}

		// JSON payload; validated via json_decode() + Sanitizer::config() below.
		// sanitize_text_field() would corrupt it.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw = wp_unslash( $_POST[ self::DATA_FIELD ] );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$decoded = is_string( $raw ) ? json_decode( $raw, true ) : null;

		if ( ! is_array( $decoded ) ) {
			return $data;
		}

		$group     = FieldGroup::from_array( $decoded );
		$sanitized = Sanitizer::config( $group->to_array() );

		$data['post_content'] = wp_slash( (string) wp_json_encode( $sanitized ) );

		return $data;
	}
}
