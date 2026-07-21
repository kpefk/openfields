/**
 * E2E: Field Group Builder — create, add fields, save, reload.
 *
 * Covers acceptance criteria §8.2 (fields render and save) and the reload half
 * of §8.6 (persisted configuration is reproduced).
 *
 * Prerequisites: a running wp-env (`bun run env:start`) and the e2e deps (see
 * tests/e2e/README.md). Run with `bun run test:e2e`. This spec cannot run inside
 * the sandboxed CI harness (no WordPress); run it locally or in a CI job that
 * provisions wp-env.
 */

import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Field Group Builder', () => {
	test( 'creates a group, adds three fields and persists them', async ( {
		admin,
		page,
	} ) => {
		await admin.visitAdminPage(
			'post-new.php',
			'post_type=openfields-group'
		);

		await page
			.getByLabel( 'Field group title' )
			.fill( 'Hero fields' );

		for ( let i = 0; i < 3; i++ ) {
			await page
				.getByRole( 'button', { name: 'Add field' } )
				.click();
		}

		await expect(
			page.locator( '.openfields-field-row' )
		).toHaveCount( 3 );

		// Save via the classic editor Publish button, then wait for the reload.
		await Promise.all( [
			page.waitForNavigation(),
			page.locator( '#publish' ).click(),
		] );

		// The saved configuration is re-hydrated into the builder on reload.
		await expect(
			page.locator( '.openfields-field-row' )
		).toHaveCount( 3 );
	} );
} );
