# Changelog

All notable changes to OpenFields are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
While the version is `0.x`, the public API and data formats may change between
minor releases.

## [Unreleased]

### Added
- Project scaffold: plugin bootstrap, Composer/Bun tooling, TypeScript (strict)
  build via `@wordpress/scripts`, `@wordpress/env` dev environment.
- Quality gates: PHPCS (WordPress Coding Standards), PHPStan level 6, ESLint,
  `tsc --noEmit`, PHPUnit, and WordPress Plugin Check — all wired into GitHub
  Actions CI.
- Guard that safely deactivates OpenFields (with an admin notice) when a
  conflicting ACF installation is active.
- Full data cleanup on uninstall, including multisite.
- Core layer: a small dependency-injection `Container`; the `openfields-group`
  custom post type and its `openfields_disabled` status; a centralized
  `Security` helper for nonces and capabilities; activation that grants the
  `edit_field_groups` / `manage_options_pages` capabilities; a `MetaRegistrar`
  that registers field-value meta with a REST-safe, scalar-typed schema; and an
  `Assets` loader for compiled admin bundles — all wired through `Plugin`.
- Unit tests for the Core layer (WordPress functions stubbed via namespaced
  overrides).
- Field groups layer: a `FieldGroup` model that normalises configuration and
  applies schema migrations; a `SchemaUpgrader` performing sequential,
  idempotent, lazy migrations with a `schema_version` stamp; a pure
  `LocationRules` engine (OR-groups of AND-rules, `==`/`!=`, extensible
  providers) evaluated against an immutable `LocationContext`; a versioned
  `LocationCache` invalidated when a field group is saved or deleted; and a
  `Support\Sanitizer` for keys and configuration structures.
- Field Type API (backend): an `AbstractFieldType` contract (sanitize, validate
  returning `WP_Error`, format, REST/JSON schema, required handling) and a
  `FieldTypeRegistry` with an `openfields/register_field_types` extension hook,
  plus the 13 core field types — Text, Textarea, Number, Email, URL, Image,
  File, WYSIWYG, Select, Checkbox, Radio, True/False and Message.
- Field Type API (client): a TypeScript contract (`FieldConfig` and friends
  generated from `schemas/field-config.schema.json` via `generate:types`, plus
  the React-facing `FieldTypeDefinition` / `FieldEditProps` interfaces), a
  `registerFieldType` registry mirroring the PHP one, and the first three field
  components — Text, Number and True/False — registered on load. Frontend Jest
  tests run via `@wordpress/scripts`.
- Field Group Builder: a React app (pure reducer + `@dnd-kit` drag-and-drop with
  keyboard support) to add, reorder, duplicate, delete and configure fields,
  edit per-field conditional logic, define location rules, and set group
  options. Mounted on the `openfields-group` edit screen, it serializes to a
  hidden input; a `wp_insert_post_data` handler validates the nonce/capability,
  sanitizes the payload and persists it to the post content. A Playwright e2e
  spec covers the create → add → save → reload flow.
- Client components for the remaining core field types — Textarea, Email, URL,
  Image, File, WYSIWYG, Select, Checkbox, Radio and Message — completing all 13
  core types on the front end. Image/File share a media-library picker; choice
  types read their options from `settings.choices`; a shared `requiredError`
  helper drives client-side required validation.
- Record form: field groups whose location rules match a post edit screen now
  render as meta boxes (matches cached via `LocationCache`). A `FieldGroupRepository`
  enumerates groups, a `ValueStore` sanitizes values by field type and persists
  them to post meta (reading them back to seed the form), and the React
  `RecordForm` reuses each field type's edit component. Values are saved on
  `save_post` after nonce and `edit_post` capability checks.
- Conditional logic and validation feedback in the record form: fields show and
  hide live as their dependencies change (OR-of-AND rules with `==`, `!=`, `>`,
  `<`, `contains`, `empty`, `not_empty`, `matches`). A PHP `Validator` checks
  values on save and produces a `WP_Error` keyed by field key — the standardized
  `{ field_key: message }` contract — which is stored per post/user and surfaced
  back to the form to highlight invalid fields; the form also validates touched
  fields client-side.
- Public PHP API: `get_field()`, `get_fields()` and `update_field()` (backed by
  a `FieldResolver` that looks up field configs from a `wp_cache`-backed map and
  formats/sanitizes by field type), `have_rows()` / `the_row()` scaffolds,
  `openfields_add_local_field_group()` (programmatic groups via a `LocalStore`
  the repository merges in), `openfields_register_field_type()`, and an
  `openfields_add_options_page()` signature. New extension hooks
  (`openfields/load_value`, `openfields/format_value`, `openfields/updated_value`,
  `openfields/register_options_page`) are documented in `docs/hooks-reference.md`.

[Unreleased]: https://github.com/kpefk/openfields/commits/main
