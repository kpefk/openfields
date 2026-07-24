# Getting Started

## Install & activate

1. Copy the plugin to `wp-content/plugins/openfields` (or install the release
   zip), then activate **OpenFields** on the Plugins screen.
2. OpenFields refuses to activate alongside Advanced Custom Fields — deactivate
   one of them, as they provide the same functions.

## Create a field group

1. Go to **Field Groups → Add New**.
2. Give the group a title, then add fields (Text, Number, Image, Select, …).
   Each field has a label, a name (its storage key), instructions, a *required*
   toggle, and optional conditional logic.
3. Under **Location Rules**, choose where the group appears (e.g. Post Type is
   equal to Page).
4. Publish.

## Edit values

Open a post that matches the location rules. The fields appear as a meta box in
the classic editor, or in a document settings panel in the block editor. Fields
show and hide live according to their conditional logic.

## Read values in a template

```php
the_field_headline: <?php echo esc_html( get_field( 'headline' ) ); ?>
```

See the [README](../README.md#usage) for the full API, and
[docs/rest-api.md](rest-api.md) for reading values over REST.

## Sync between environments (Local JSON)

Create an `openfields-json/` directory in your theme. Export a group as JSON from
**Field Groups → Import & Export**, save it there as `{group_key}.json`, and it
will be registered automatically (overriding the database copy). Commit that
directory to move field-group configuration between environments.
