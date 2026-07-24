# Public PHP API

All functions are available once the plugin has booted (after `plugins_loaded`).

## Reading & writing values

### `get_field( string $selector, $post_id = false, bool $format_value = true )`

Return a single field value. `$selector` is the field **name** or **key**;
`$post_id` defaults to the current post. With `$format_value = false` the raw
stored value is returned instead of the field type's formatted value.

```php
$headline = get_field( 'headline' );
$image_id = get_field( 'hero_image', $post_id, false );
```

### `get_fields( $post_id = false ): array`

Return every field value for a post, keyed by field name.

### `update_field( string $selector, $value, $post_id = false ): bool`

Sanitize (by field type) and store a value. Returns whether it was written.

### `have_rows()` / `the_row()`

Scaffolds for the Repeater field (Phase 2). `have_rows()` currently returns
`false`.

## Registering field groups

### `openfields_add_local_field_group( array $config ): void`

Register a field group in code (not stored in the database). Locally-registered
groups override database groups that share the same key.

```php
openfields_add_local_field_group( array(
	'key'      => 'group_hero',
	'title'    => 'Hero',
	'location' => array(
		array(
			array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ),
		),
	),
	'fields'   => array( /* field configs — see below */ ),
) );
```

### `openfields_register_field_type( string $class ): void`

Register a custom field type by class name (must extend
`OpenFields\FieldTypes\AbstractFieldType`). See
[field-types-api.md](field-types-api.md).

### `openfields_add_options_page( array $config ): void`

Signature is stable; the full Options Pages implementation ships in a later
phase. Fires the `openfields/register_options_page` action.

## Field group configuration

| Key | Type | Description |
|-----|------|-------------|
| `key` | string | Unique group key. |
| `title` | string | Group title. |
| `fields` | array | List of field configs. |
| `location` | array | OR-groups of AND-rules (see below). |
| `settings` | array | `position` (`normal`/`side`/`acf_after_title`), `style` (`default`/`seamless`), `label_placement` (`top`/`left`), `instruction_placement` (`label`/`field`), `menu_order`, `description`. |

### Field configuration

| Key | Type | Description |
|-----|------|-------------|
| `key` | string | Globally unique field key. |
| `name` | string | Meta name used to store the value. |
| `label` | string | Display label. |
| `type` | string | Field type identifier — see [field-types.md](field-types.md). |
| `instructions` | string | Help text. |
| `required` | bool | Whether a value is required. |
| `defaultValue` | mixed | Default when empty. |
| `placeholder` | string | Input placeholder. |
| `conditionalLogic` | array\|false | Visibility rules (see below). |
| `wrapper` | array | `width`, `class`, `id`. |
| `settings` | array | Type-specific settings — see [field-types.md](field-types.md). |

## Location rules

`location` is a list of **OR-groups**; each group is a list of **AND-rules**.
The group applies when any group fully matches.

```php
'location' => array(
	// Group A: post type is page AND user is an administrator …
	array(
		array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ),
		array( 'param' => 'user_role', 'operator' => '==', 'value' => 'administrator' ),
	),
	// … OR Group B: post type is post.
	array(
		array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ),
	),
),
```

**Operators:** `==`, `!=`.

**Parameters:** `post_type`, `post_status`, `post_format`, `page_template`,
`page_type`, `taxonomy`, `post_term`, `user_role`, `editor`, `options_page`.
Add custom parameters with the `openfields/location_providers` filter.

## Conditional logic

`conditionalLogic` toggles a field's visibility based on other fields, using the
same OR-of-AND structure. Rules reference a dependency field by its **key**.

```php
'conditionalLogic' => array(
	array(
		array( 'field' => 'field_show_extra', 'operator' => '==', 'value' => '1' ),
	),
),
```

**Operators:** `==`, `!=`, `>`, `<`, `contains`, `empty`, `not_empty`, `matches`
(regular expression).
