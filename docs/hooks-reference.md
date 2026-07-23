# Hooks Reference

All OpenFields hooks are namespaced with the `openfields/` prefix.

## Actions

| Hook | Fired | Arguments |
|------|-------|-----------|
| `openfields/booted` | After the plugin boots. | `$container` (service container) |
| `openfields/register_field_types` | After the built-in field types register; add custom types here. | `$registry` (`FieldTypeRegistry`) |
| `openfields/register_meta` | When field-value meta keys should be registered. | `$registrar` (`MetaRegistrar`) |
| `openfields/updated_value` | After a field value is written via `update_field()`. | `$selector`, `$value`, `$post_id`, `$field` |
| `openfields/register_options_page` | When an options page is registered via `openfields_add_options_page()`. | `$config` |

## Filters

| Hook | Filters | Arguments |
|------|---------|-----------|
| `openfields/location_providers` | The map of location-rule parameter providers. | `$providers` |
| `openfields/load_value` | A raw field value as it is loaded. | `$value`, `$selector`, `$post_id`, `$field` |
| `openfields/format_value` | A formatted field value. | `$formatted`, `$value`, `$selector`, `$post_id`, `$field` |

## Example: registering a custom field type

```php
add_action( 'openfields/register_field_types', function ( $registry ) {
	$registry->register( new \Acme\RatingFieldType() );
} );
```

Or, by class name:

```php
openfields_register_field_type( \Acme\RatingFieldType::class );
```
