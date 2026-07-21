<?php
/**
 * Field-group model.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldGroups;

use OpenFields\Core\PostType;

defined( 'ABSPATH' ) || exit;

/**
 * An immutable field-group configuration.
 *
 * A field group is stored as an `openfields-group` post whose content is the
 * JSON configuration. This model normalises that configuration (applying
 * defaults and schema migrations) and exposes typed accessors.
 */
final class FieldGroup {

	/**
	 * Normalised configuration.
	 *
	 * @var array<string, mixed>
	 */
	private array $data;

	/**
	 * Wrap an already-normalised configuration.
	 *
	 * @param array<string, mixed> $data Normalised configuration.
	 */
	private function __construct( array $data ) {
		$this->data = $data;
	}

	/**
	 * Build a field group from a configuration array.
	 *
	 * Applies defaults and upgrades the configuration to the current schema.
	 *
	 * @param array<string, mixed> $data     Raw configuration.
	 * @param SchemaUpgrader|null  $upgrader Optional upgrader (created if omitted).
	 * @return FieldGroup
	 */
	public static function from_array( array $data, ?SchemaUpgrader $upgrader = null ): self {
		$upgrader = $upgrader ?? new SchemaUpgrader();

		return new self( $upgrader->upgrade( self::with_defaults( $data ) ) );
	}

	/**
	 * Build a field group from an `openfields-group` post.
	 *
	 * @param int|\WP_Post        $post     Post ID or object.
	 * @param SchemaUpgrader|null $upgrader Optional upgrader.
	 * @return FieldGroup|null Null when the post is not a field group.
	 */
	public static function from_post( $post, ?SchemaUpgrader $upgrader = null ): ?self {
		$post = get_post( $post );

		if ( ! $post instanceof \WP_Post || PostType::POST_TYPE !== $post->post_type ) {
			return null;
		}

		$decoded = json_decode( (string) $post->post_content, true );
		$data    = is_array( $decoded ) ? $decoded : array();

		$data['key']    = isset( $data['key'] ) ? (string) $data['key'] : 'group_' . $post->ID;
		$data['title']  = isset( $data['title'] ) ? (string) $data['title'] : (string) $post->post_title;
		$data['active'] = PostType::STATUS_DISABLED !== $post->post_status;

		return self::from_array( $data, $upgrader );
	}

	/**
	 * The group key.
	 *
	 * @return string
	 */
	public function key(): string {
		return (string) $this->data['key'];
	}

	/**
	 * The group title.
	 *
	 * @return string
	 */
	public function title(): string {
		return (string) $this->data['title'];
	}

	/**
	 * The group's fields.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function fields(): array {
		return is_array( $this->data['fields'] ) ? $this->data['fields'] : array();
	}

	/**
	 * The group's location rules (OR-groups of AND-rules).
	 *
	 * @return array<int, array<int, array<string, mixed>>>
	 */
	public function location(): array {
		return is_array( $this->data['location'] ) ? $this->data['location'] : array();
	}

	/**
	 * The group's presentation settings.
	 *
	 * @return array<string, mixed>
	 */
	public function settings(): array {
		return is_array( $this->data['settings'] ) ? $this->data['settings'] : array();
	}

	/**
	 * The schema version of the configuration.
	 *
	 * @return int
	 */
	public function schema_version(): int {
		return (int) $this->data['schema_version'];
	}

	/**
	 * Whether the group is active (as opposed to disabled).
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return (bool) $this->data['active'];
	}

	/**
	 * The full normalised configuration.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return $this->data;
	}

	/**
	 * The configuration encoded as JSON.
	 *
	 * @return string
	 */
	public function to_json(): string {
		return (string) wp_json_encode( $this->data );
	}

	/**
	 * Merge a raw configuration with defaults.
	 *
	 * @param array<string, mixed> $data Raw configuration.
	 * @return array<string, mixed>
	 */
	private static function with_defaults( array $data ): array {
		$defaults = array(
			'key'      => '',
			'title'    => '',
			'fields'   => array(),
			'location' => array(),
			'active'   => true,
			'settings' => array(),
		);

		$data = array_merge( $defaults, $data );

		$data['settings'] = array_merge(
			array(
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'menu_order'            => 0,
				'description'           => '',
				'hide_on_screen'        => array(),
			),
			is_array( $data['settings'] ) ? $data['settings'] : array()
		);

		return $data;
	}
}
