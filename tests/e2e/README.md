# End-to-end tests

E2E specs run against a real WordPress via [`@wordpress/env`][wp-env] using
Playwright and `@wordpress/e2e-test-utils-playwright`.

They **cannot** run inside the sandboxed CI harness (which has no WordPress); run
them locally or in a CI job that provisions wp-env.

## Prerequisites

```bash
# 1. Build the plugin assets
bun run build

# 2. Start WordPress (Docker)
bun run env:start

# 3. One-time: install the e2e dependencies and browser
bun add -d @playwright/test @wordpress/e2e-test-utils-playwright @axe-core/playwright
bunx playwright install chromium
```

## Run

```bash
bun run test:e2e
```

## Coverage

- `field-group-builder.spec.ts` — create a field group, add three fields, save,
  reload, and assert the fields persist (acceptance criteria §8.2 and the reload
  half of §8.6).

Accessibility assertions (axe-core) should be added here as the builder UI
stabilises; keyboard reordering is provided by `@dnd-kit`'s keyboard sensor.

[wp-env]: https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/
