# OpenFields

**Open Source custom fields, field groups and content builders for WordPress — a GPL-compatible alternative to ACF PRO.**

[![CI](https://github.com/kpefk/openfields/actions/workflows/ci.yml/badge.svg)](https://github.com/kpefk/openfields/actions/workflows/ci.yml)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

> ⚠️ **Pre-release (0.x).** OpenFields is under active development. APIs and data
> formats may change until 1.0.0. Not yet recommended for production.

## What is OpenFields?

OpenFields lets you build custom fields, field groups, repeaters, flexible
content and options pages for WordPress — with deep Gutenberg, REST API and
GraphQL integration, and no proprietary license restrictions. It is designed to
feel familiar to ACF users while remaining fully GPL and forkable.

## Requirements

| Component | Version |
|-----------|---------|
| WordPress | 6.4+ |
| PHP       | 8.1+ |
| MySQL / MariaDB | 5.7+ / 10.3+ |

## Usage

Create field groups in **Field Groups** in the admin, or register them in code:

```php
openfields_add_local_field_group( array(
	'key'      => 'group_hero',
	'title'    => 'Hero',
	'location' => array(
		array(
			array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ),
		),
	),
	'fields'   => array(
		array( 'key' => 'field_headline', 'name' => 'headline', 'label' => 'Headline', 'type' => 'text', 'required' => true ),
		array( 'key' => 'field_image', 'name' => 'hero_image', 'label' => 'Image', 'type' => 'image' ),
	),
) );
```

Read and write values in your templates:

```php
$headline = get_field( 'headline' );          // current post
$image_id = get_field( 'hero_image', $post_id );
update_field( 'headline', 'Welcome', $post_id );
$all = get_fields( $post_id );                 // every field, keyed by name
```

Full documentation is in [docs/](docs/README.md): the
[public API](docs/public-api.md), [built-in field types](docs/field-types.md),
the [Field Type API](docs/field-types-api.md),
[hooks](docs/hooks-reference.md) and the [REST API](docs/rest-api.md).

## Development

This project uses **Composer** (PHP) and **Bun** (JS/TS), and **`@wordpress/env`**
(Docker) for a local WordPress environment.

```bash
# Install dependencies
composer install
bun install

# Build admin assets (TypeScript + React)
bun run build

# Start a local WordPress dev site (requires Docker)
bunx wp-env start
```

The plugin is mounted automatically; visit the printed URL and activate
**OpenFields** from the Plugins screen.

### Common scripts

| Command | Purpose |
|---------|---------|
| `bun run start` | Dev build with watch (regenerates types first) |
| `bun run type-check` | `tsc --noEmit` (strict) — blocks merges on type errors |
| `bun run generate:types` | Generate `.d.ts` from JSON Schemas |
| `bun run lint:js` | ESLint |
| `composer run phpcs` | PHP_CodeSniffer (WordPress Coding Standards) |
| `composer run phpstan` | Static analysis (level 6) |
| `composer run test` | PHPUnit |

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Issues and pull requests are welcome.

## License

[GPL-2.0-or-later](LICENSE).
