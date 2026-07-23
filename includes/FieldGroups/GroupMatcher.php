<?php
/**
 * Resolves the field groups that apply to a given post edit screen.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the location context for a post and returns the matching groups,
 * caching the result (the hottest path in the admin) per screen context.
 */
final class GroupMatcher {

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
	 * Build the matcher with its collaborators.
	 *
	 * @param FieldGroupRepository $repository Field group repository.
	 * @param LocationRules        $rules      Location rules engine.
	 * @param LocationCache        $cache      Location match cache.
	 */
	public function __construct(
		FieldGroupRepository $repository,
		LocationRules $rules,
		LocationCache $cache
	) {
		$this->repository = $repository;
		$this->rules      = $rules;
		$this->cache      = $cache;
	}

	/**
	 * The active field groups whose location rules match the given post.
	 *
	 * @param \WP_Post $post Current post.
	 * @return FieldGroup[]
	 */
	public function for_post( \WP_Post $post ): array {
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
