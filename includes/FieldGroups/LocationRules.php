<?php
/**
 * Location-rules matching engine.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

defined( 'ABSPATH' ) || exit;

/**
 * Evaluates a field group's location rules against a {@see LocationContext}.
 *
 * Rules are organised as OR-groups of AND-rules (matching ACF's structure):
 * the location matches when *any* group matches, and a group matches when *all*
 * of its rules match. Each rule is `{ param, operator, value }` where the
 * operator is `==` or `!=`.
 *
 * A "provider" maps a rule parameter (e.g. `post_type`) to the list of
 * candidate values in the current context. Third parties can add providers via
 * the `openfields/location_providers` filter.
 */
final class LocationRules {

	/**
	 * Candidate-value providers keyed by parameter name.
	 *
	 * @var array<string, callable(LocationContext):array<int, string>>
	 */
	private array $providers;

	/**
	 * Build the engine with the default providers, or the supplied overrides.
	 *
	 * @param array<string, callable(LocationContext):array<int, string>> $providers Optional provider overrides.
	 */
	public function __construct( array $providers = array() ) {
		$this->providers = array() === $providers ? self::default_providers() : $providers;
	}

	/**
	 * Whether the given location rules match the context.
	 *
	 * An empty rule set matches everything.
	 *
	 * @param array<int, array<int, array<string, mixed>>> $location Location rule groups.
	 * @param LocationContext                              $context  Screen context.
	 * @return bool
	 */
	public function matches( array $location, LocationContext $context ): bool {
		if ( array() === $location ) {
			return true;
		}

		foreach ( $location as $group ) {
			if ( is_array( $group ) && $this->group_matches( $group, $context ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter a list of field groups down to those whose location matches.
	 *
	 * @param FieldGroup[]    $groups  Field groups to test.
	 * @param LocationContext $context Screen context.
	 * @return FieldGroup[]
	 */
	public function matching_groups( array $groups, LocationContext $context ): array {
		return array_values(
			array_filter(
				$groups,
				fn ( FieldGroup $group ): bool => $this->matches( $group->location(), $context )
			)
		);
	}

	/**
	 * Whether every rule in an AND-group matches.
	 *
	 * @param array<int, array<string, mixed>> $rules   AND-group of rules.
	 * @param LocationContext                  $context Screen context.
	 * @return bool
	 */
	private function group_matches( array $rules, LocationContext $context ): bool {
		foreach ( $rules as $rule ) {
			if ( ! is_array( $rule ) || ! $this->rule_matches( $rule, $context ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Whether a single rule matches the context.
	 *
	 * @param array<string, mixed> $rule    A `{ param, operator, value }` rule.
	 * @param LocationContext      $context Screen context.
	 * @return bool
	 */
	private function rule_matches( array $rule, LocationContext $context ): bool {
		$param    = isset( $rule['param'] ) ? (string) $rule['param'] : '';
		$operator = isset( $rule['operator'] ) ? (string) $rule['operator'] : '==';
		$value    = isset( $rule['value'] ) ? (string) $rule['value'] : '';

		$candidates = $this->candidates( $param, $context );
		$present    = in_array( $value, $candidates, true );

		return '!=' === $operator ? ! $present : $present;
	}

	/**
	 * Resolve the candidate values for a parameter in the given context.
	 *
	 * @param string          $param   Parameter name.
	 * @param LocationContext $context Screen context.
	 * @return string[]
	 */
	private function candidates( string $param, LocationContext $context ): array {
		/**
		 * Filters the location-rule providers.
		 *
		 * @since 0.1.0
		 *
		 * @param array<string, callable> $providers Providers keyed by parameter name.
		 */
		$providers = apply_filters( 'openfields/location_providers', $this->providers );

		if ( ! isset( $providers[ $param ] ) ) {
			return array();
		}

		$values = ( $providers[ $param ] )( $context );

		return array_map( 'strval', is_array( $values ) ? $values : array() );
	}

	/**
	 * The built-in parameter providers.
	 *
	 * @return array<string, callable(LocationContext):array<int, string>>
	 */
	private static function default_providers(): array {
		return array(
			'post_type'     => static fn ( LocationContext $c ): array => $c->get_list( 'post_type' ),
			'post_status'   => static fn ( LocationContext $c ): array => $c->get_list( 'post_status' ),
			'post_format'   => static fn ( LocationContext $c ): array => $c->get_list( 'post_format' ),
			'page_template' => static fn ( LocationContext $c ): array => $c->get_list( 'page_template' ),
			'page_type'     => static fn ( LocationContext $c ): array => $c->get_list( 'page_type' ),
			'taxonomy'      => static fn ( LocationContext $c ): array => $c->get_list( 'taxonomies' ),
			'post_term'     => static fn ( LocationContext $c ): array => $c->get_list( 'post_terms' ),
			'user_role'     => static fn ( LocationContext $c ): array => $c->get_list( 'user_roles' ),
			'editor'        => static fn ( LocationContext $c ): array => $c->get_list( 'editor' ),
			'options_page'  => static fn ( LocationContext $c ): array => $c->get_list( 'options_page' ),
		);
	}
}
