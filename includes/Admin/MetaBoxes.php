<?php
/**
 * Field-group meta boxes on post edit screens.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Admin;

use OpenFields\Core\PostType;
use OpenFields\Core\Security;
use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\FieldGroupRepository;
use OpenFields\FieldGroups\LocationCache;
use OpenFields\FieldGroups\LocationContext;
use OpenFields\FieldGroups\LocationRules;
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
	 * Location rules engine.
	 *
	 * @var LocationRules
	 */
	private LocationRules $rules;

	/**
	 * Location match cache.
	 *
	 * @var LocationCache
	 */
	private LocationCache $cache;

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
	 * Build the meta boxes controller with its collaborators.
	 *
	 * @param FieldGroupRepository $repository Field group repository.
	 * @param LocationRules        $rules      Location rules engine.
	 * @param LocationCache        $cache      Location match cache.
	 * @param ValueStore           $values     Value store.
	 * @param Security             $security   Security helper.
	 */
	public function __construct(
		FieldGroupRepository $repository,
		LocationRules $rules,
		LocationCache $cache,
		ValueStore $values,
		Security $security
	) {
		$this->repository = $repository;
		$this->rules      = $rules;
		$this->cache      = $cache;
		$this->values     = $values;
		$this->security   = $security;
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

		foreach ( $this->matching_groups( $post ) as $group ) {
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

		foreach ( $records as $group_key => $json ) {
			if ( ! is_string( $json ) ) {
				continue;
			}

			$group = $this->repository->find( (string) $group_key );

			if ( null === $group ) {
				continue;
			}

			$decoded = json_decode( $json, true );

			if ( is_array( $decoded ) ) {
				$this->values->save( $post_id, $group, $decoded );
			}
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
			'<div class="openfields-record-form" data-input-id="%1$s" data-group="%2$s" data-values="%3$s"></div>',
			esc_attr( $input_id ),
			esc_attr( (string) wp_json_encode( $group->to_array() ) ),
			esc_attr( (string) wp_json_encode( $values ) )
		);
	}

	/**
	 * Resolve the field groups matching the current screen, cached per context.
	 *
	 * @param \WP_Post $post Current post.
	 * @return FieldGroup[]
	 */
	private function matching_groups( \WP_Post $post ): array {
		$context = $this->build_context( $post );
		$hash    = md5( (string) wp_json_encode( $context->to_array() ) );

		$cached = $this->cache->remember(
			$hash,
			fn (): array => $this->rules->matching_groups(
				$this->repository->active(),
				$context
			)
		);

		$groups = array();

		foreach ( is_array( $cached ) ? $cached : array() as $group ) {
			if ( $group instanceof FieldGroup ) {
				$groups[] = $group;
			}
		}

		return $groups;
	}

	/**
	 * Build the location context from the current post and user.
	 *
	 * @param \WP_Post $post Current post.
	 * @return LocationContext
	 */
	private function build_context( \WP_Post $post ): LocationContext {
		$user     = wp_get_current_user();
		$template = get_page_template_slug( $post );

		return new LocationContext(
			array(
				'post_type'     => $post->post_type,
				'post_status'   => $post->post_status,
				'page_template' => '' !== $template ? $template : 'default',
				'user_roles'    => $user instanceof \WP_User ? $user->roles : array(),
			)
		);
	}
}
