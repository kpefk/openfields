<?php
/**
 * Registry of available field types.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\FieldTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Holds the set of registered field types keyed by their type identifier.
 *
 * The built-in types are registered by {@see FieldTypeRegistry::register_defaults()},
 * after which the `openfields/register_field_types` action lets third parties add
 * their own.
 */
final class FieldTypeRegistry {

	/**
	 * Registered types keyed by type identifier.
	 *
	 * @var array<string, AbstractFieldType>
	 */
	private array $types = array();

	/**
	 * Register a field type, replacing any existing type with the same key.
	 *
	 * @param AbstractFieldType $type Field type instance.
	 * @return void
	 */
	public function register( AbstractFieldType $type ): void {
		$this->types[ $type->get_type() ] = $type;
	}

	/**
	 * Whether a type identifier is registered.
	 *
	 * @param string $type Type identifier.
	 * @return bool
	 */
	public function has( string $type ): bool {
		return isset( $this->types[ $type ] );
	}

	/**
	 * Retrieve a registered type.
	 *
	 * @param string $type Type identifier.
	 * @return AbstractFieldType|null
	 */
	public function get( string $type ): ?AbstractFieldType {
		return $this->types[ $type ] ?? null;
	}

	/**
	 * All registered types keyed by identifier.
	 *
	 * @return array<string, AbstractFieldType>
	 */
	public function all(): array {
		return $this->types;
	}

	/**
	 * Register the built-in field types and let third parties add their own.
	 *
	 * @return void
	 */
	public function register_defaults(): void {
		foreach ( self::default_types() as $type ) {
			$this->register( $type );
		}

		/**
		 * Fires after the built-in field types are registered.
		 *
		 * @since 0.1.0
		 *
		 * @param FieldTypeRegistry $registry The field type registry.
		 */
		do_action( 'openfields/register_field_types', $this );
	}

	/**
	 * The built-in field type instances.
	 *
	 * @return AbstractFieldType[]
	 */
	private static function default_types(): array {
		return array(
			new Types\Text(),
			new Types\Textarea(),
			new Types\Number(),
			new Types\Email(),
			new Types\Url(),
			new Types\Image(),
			new Types\File(),
			new Types\Wysiwyg(),
			new Types\Select(),
			new Types\Checkbox(),
			new Types\Radio(),
			new Types\TrueFalse(),
			new Types\Message(),
		);
	}
}
