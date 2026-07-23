<?php
/**
 * Field-group meta boxes on post edit screens.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Admin;

use OpenFields\Core\Assets;
use OpenFields\Core\PostType;
use OpenFields\Core\Security;
use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldGroups\GroupMatcher;
use OpenFields\FieldGroups\Validator;
use OpenFields\FieldGroups\ValueStore;

defined( 'ABSPATH' ) || exit;

/**
 * Registers a meta box for each field group whose location rules match the
 * current post edit screen, renders the React record form container, and saves
 * the submitted values.
 */
final class MetaBoxes {

	private const NONCE_ACTION = 'save_record';
	private const NONCE_FIELD  = 'openfields_record_nonce';

	/**
	 * Field group repository.
	 *
	 * @var FieldGroupRepository
	 */
	private FieldGroupRepository $repository;

	/**
	 * Group matcher.
	 *
	 * @var GroupMatcher
	 */
	private GroupMatcher $matcher;

	/**
	 * Value store.
	 *
	 * @var ValueStore
	 */
	private ValueStore $values;

	/**
	 * Security helper.
	 *
	 * @var Security
	 */
	private Security $security;

	/**
	 * Value validator.
	 *
	 * @var Validator
	 */
	private Validator $validator;

	/**
	 * Asset enqueuer.
	 *
	 * @var Assets
	 */
	private Assets $assets;

	/**
	 * Build the meta boxes controller with its collaborators.
	 *
	 * @param FieldGroupRepository $repository Field group repository.
	 * @param GroupMatcher         $matcher    Group matcher.
	 * @param ValueStore           $values     Value store.
	 * @param Security             $security   Security helper.
	 * @param Validator            $validator  Value validator.
	 * @param Assets               $assets     Asset enqueuer.
	 */
	public function __construct(
		FieldGroupRepository $repository,
		GroupMatcher $matcher,
		ValueStore $values,
		Security $security,
		Validator $validator,
		Assets $assets
	) {
		$this->repository = $repository;
		$this->matcher    = $matcher;
		$this->values     = $values;
		$this->security   = $security;
		$this->validator  = $validator;
		$this->assets     = $assets;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'add_meta_boxes', array( $this, 'add' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Add a meta box for each matching field group.
	 *
	 * @param string   $post_type Current post type.
	 * @param \WP_Post $post      Current post.
	 * @return void
	 */
	public function add( string $post_type, \WP_Post $post ): void {
		if ( PostType::POST_TYPE === $post_type ) {
			return;
		}

		$screen = get_current_screen();

		// In the block editor the Gutenberg sidebar renders the fields instead.
		if ( $screen instanceof \WP_Screen && $screen->is_block_editor() ) {
			return;
		}

		$groups = $this->matcher->for_post( $post );

		if ( array() === $groups ) {
			return;
		}

		$this->assets->enqueue_app();

		foreach ( $groups as $group ) {
			$settings = $group->settings();
			$position = isset( $settings['position'] ) ? (string) $settings['position'] : 'normal';
			$context  = 'side' === $position ? 'side' : 'normal';

			add_meta_box(
				'openfields-group-' . $group->key(),
				$group->title() !== '' ? $group->title() : __( 'Field Group', 'openfields' ),
				function () use ( $group, $post ): void {
					$this->render( $group, $post );
				},
				$post_type,
				$context,
				'default'
			);
		}
	}

	/**
	 * Persist submitted field values.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post being saved.
	 * @return void
	 */
	public function save( int $post_id, \WP_Post $post ): void {
		if ( PostType::POST_TYPE === $post->post_type ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// WPCS cannot see nonce verification through Security::verify_nonce();
		// it is performed explicitly below before any data is used.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST[ self::NONCE_FIELD ], $_POST['openfields_record'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) );

		if ( ! $this->security->verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! is_array( $_POST['openfields_record'] ) ) {
			return;
		}

		// Values are JSON blobs, validated via json_decode() and sanitized
		// per-field by ValueStore using each field type's sanitizer.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$records = wp_unslash( $_POST['openfields_record'] );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$error_map = array();

		foreach ( $records as $group_key => $json ) {
			if ( ! is_string( $json ) ) {
				continue;
			}

			$group = $this->repository->find( (string) $group_key );

			if ( null === $group ) {
				continue;
			}

			$decoded = json_decode( $json, true );

			if ( ! is_array( $decoded ) ) {
				continue;
			}

			$this->values->save( $post_id, $group, $decoded );

			$errors = $this->validator->validate( $group, $decoded );

			if ( $errors->has_errors() ) {
				$error_map = array_merge( $error_map, Validator::to_map( $errors ) );
			}
		}

		$transient = $this->errors_transient_key( $post_id );

		if ( array() !== $error_map ) {
			set_transient( $transient, $error_map, MINUTE_IN_SECONDS * 5 );
		} else {
			delete_transient( $transient );
		}
	}

	/**
	 * Render the record-form container for a group.
	 *
	 * @param FieldGroup $group Field group.
	 * @param \WP_Post   $post  Current post.
	 * @return void
	 */
	private function render( FieldGroup $group, \WP_Post $post ): void {
		$values   = $this->values->read( (int) $post->ID, $group );
		$input_id = 'openfields-record-input-' . $group->key();

		$stored_errors = get_transient( $this->errors_transient_key( (int) $post->ID ) );
		$errors        = is_array( $stored_errors ) ? $stored_errors : array();

		printf(
			'<input type="hidden" name="%1$s" value="%2$s" />',
			esc_attr( self::NONCE_FIELD ),
			esc_attr( $this->security->create_nonce( self::NONCE_ACTION ) )
		);
		printf(
			'<input type="hidden" id="%1$s" name="openfields_record[%2$s]" value="" />',
			esc_attr( $input_id ),
			esc_attr( $group->key() )
		);
		printf(
			'<div class="openfields-record-form" data-input-id="%1$s" data-group="%2$s" data-values="%3$s" data-errors="%4$s"></div>',
			esc_attr( $input_id ),
			esc_attr( (string) wp_json_encode( $group->to_array() ) ),
			esc_attr( (string) wp_json_encode( $values ) ),
			esc_attr( (string) wp_json_encode( $errors ) )
		);
	}

	/**
	 * Transient key holding the last save's validation errors for a post/user.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function errors_transient_key( int $post_id ): string {
		return 'openfields_errors_' . get_current_user_id() . '_' . $post_id;
	}
}
