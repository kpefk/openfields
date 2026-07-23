<?php
/**
 * Public API functions.
 *
 * The value accessors (`get_field()`, `get_fields()`, `update_field()`,
 * `have_rows()`, `the_row()`) intentionally mirror the familiar ACF names to
 * ease migration; the plugin refuses to boot when ACF is active (see
 * openfields.php), so the names never collide at runtime. The registration
 * helpers are prefixed with `openfields_`.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- ACF-compatible public API; guarded against conflicts in openfields.php.

use OpenFields\Api\FieldResolver;
use OpenFields\Core\Plugin;
use OpenFields\FieldGroups\LocalStore;
use OpenFields\FieldTypes\AbstractFieldType;
use OpenFields\FieldTypes\FieldTypeRegistry;

/**
 * Resolve the field resolver service from the container.
 *
 * @return FieldResolver
 */
function openfields_resolver(): FieldResolver {
	return Plugin::instance()->container()->get( FieldResolver::class );
}

/**
 * Normalise a post-ID argument, defaulting to the current post.
 *
 * @param int|false $post_id Post ID, or false for the current post.
 * @return int
 */
function openfields_resolve_post_id( $post_id ): int {
	if ( false === $post_id || null === $post_id ) {
		$current = get_the_ID();

		return false === $current ? 0 : (int) $current;
	}

	return (int) $post_id;
}

if ( ! function_exists( 'get_field' ) ) {
	/**
	 * Retrieve a field value.
	 *
	 * @param string    $selector     Field name or key.
	 * @param int|false $post_id      Post ID, or false for the current post.
	 * @param bool      $format_value Whether to format the value.
	 * @return mixed
	 */
	function get_field( string $selector, $post_id = false, bool $format_value = true ) {
		return openfields_resolver()->get(
			$selector,
			openfields_resolve_post_id( $post_id ),
			$format_value
		);
	}
}

if ( ! function_exists( 'get_fields' ) ) {
	/**
	 * Retrieve all field values for a post, keyed by field name.
	 *
	 * @param int|false $post_id Post ID, or false for the current post.
	 * @return array<string, mixed>
	 */
	function get_fields( $post_id = false ): array {
		return openfields_resolver()->get_all( openfields_resolve_post_id( $post_id ) );
	}
}

if ( ! function_exists( 'update_field' ) ) {
	/**
	 * Update a field value.
	 *
	 * @param string    $selector Field name or key.
	 * @param mixed     $value    Value to store.
	 * @param int|false $post_id  Post ID, or false for the current post.
	 * @return bool
	 */
	function update_field( string $selector, $value, $post_id = false ): bool {
		return openfields_resolver()->update(
			$selector,
			$value,
			openfields_resolve_post_id( $post_id )
		);
	}
}

if ( ! function_exists( 'have_rows' ) ) {
	/**
	 * Whether a repeater/flexible field has rows to iterate.
	 *
	 * Scaffold for the Repeater field (Phase 2); currently always false.
	 *
	 * @param string    $selector Field name or key.
	 * @param int|false $post_id  Post ID, or false for the current post.
	 * @return bool
	 */
	function have_rows( string $selector, $post_id = false ): bool {
		unset( $selector, $post_id );

		return false;
	}
}

if ( ! function_exists( 'the_row' ) ) {
	/**
	 * Advance to the next repeater row.
	 *
	 * Scaffold for the Repeater field (Phase 2); currently returns an empty row.
	 *
	 * @return array<string, mixed>
	 */
	function the_row(): array {
		return array();
	}
}

/**
 * Register a field group programmatically (local, not stored in the database).
 *
 * @param array<string, mixed> $config Group configuration.
 * @return void
 */
function openfields_add_local_field_group( array $config ): void {
	$store = Plugin::instance()->container()->get( LocalStore::class );
	$store->add( $config );

	Plugin::instance()->container()->get( FieldResolver::class )->invalidate();
}

/**
 * Register a custom field type by class name.
 *
 * @param string $class_name Fully-qualified class name extending AbstractFieldType.
 * @return void
 */
function openfields_register_field_type( string $class_name ): void {
	if ( ! class_exists( $class_name ) || ! is_subclass_of( $class_name, AbstractFieldType::class ) ) {
		return;
	}

	Plugin::instance()->container()->get( FieldTypeRegistry::class )->register( new $class_name() );
}

/**
 * Register an options page.
 *
 * Signature is stable now; the full implementation ships with Options Pages in
 * a later phase. Third parties can hook `openfields/register_options_page`.
 *
 * @param array<string, mixed> $config Options page configuration.
 * @return void
 */
function openfields_add_options_page( array $config ): void {
	/**
	 * Fires when an options page is registered.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $config Options page configuration.
	 */
	do_action( 'openfields/register_options_page', $config );
}

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
