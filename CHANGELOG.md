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

[Unreleased]: https://github.com/kpefk/openfields/commits/main
