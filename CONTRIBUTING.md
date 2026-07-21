# Contributing to OpenFields

Thanks for your interest in improving OpenFields! This document explains how to
set up your environment and the standards contributions are expected to meet.

## Getting started

1. Fork and clone the repository.
2. Install dependencies:
   ```bash
   composer install
   bun install
   ```
3. Start the local environment (requires Docker):
   ```bash
   bunx wp-env start
   ```

## Coding standards

- **PHP** — PHP 8.1+, PSR-4 autoloading, WordPress Coding Standards. Run
  `composer run phpcs` (and `composer run phpcbf` to auto-fix) and
  `composer run phpstan` before opening a PR.
- **TypeScript** — all new admin, block and field-component code is written in
  TypeScript (`.ts`/`.tsx`) in `strict` mode. `bun run type-check` must pass;
  type errors block merges.
- **Internationalization** — user-facing strings go through `__()`/`_e()` (PHP,
  text domain `openfields`) and `@wordpress/i18n` (TS) from the start.
- **Security** — no `eval()`, no direct SQL without `$wpdb->prepare()`, sanitize
  input and escape output. Use the centralized nonce/capability helpers.

## Commits & pull requests

- Keep pull requests focused and describe the motivation and approach.
- Ensure the full CI suite (type-check, ESLint, PHPCS, PHPStan, PHPUnit,
  Plugin Check) passes.
- Update `CHANGELOG.md` under **Unreleased**.

## Reporting issues

Use the GitHub issue tracker. Include WordPress/PHP versions, steps to
reproduce, and expected vs. actual behaviour.
