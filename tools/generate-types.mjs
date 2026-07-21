/**
 * generate-types — compiles JSON Schemas in `schemas/` into TypeScript
 * declaration files under `assets/src/field-types/generated/`.
 *
 * JSON Schema is the single source of truth shared with the PHP layer
 * (`AbstractFieldType::get_json_schema()`), so the PHP REST contract and the
 * TypeScript front end cannot drift. A `--watch` flag re-runs on changes.
 *
 * NOTE: Milestone 0 ships a working no-op — there are no schemas yet. The full
 * `json-schema-to-typescript` pipeline lands in Крок 4а.
 */

import { readdir, mkdir } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const root = path.resolve( __dirname, '..' );
const schemasDir = path.join( root, 'schemas' );
const outDir = path.join( root, 'assets', 'src', 'field-types', 'generated' );

async function generate() {
	if ( ! existsSync( schemasDir ) ) {
		console.log( '[generate-types] no schemas/ directory yet — skipping.' );
		return;
	}

	const entries = ( await readdir( schemasDir ) ).filter( ( f ) =>
		f.endsWith( '.schema.json' )
	);

	if ( entries.length === 0 ) {
		console.log( '[generate-types] no *.schema.json files yet — skipping.' );
		return;
	}

	await mkdir( outDir, { recursive: true } );

	// TODO(Крок 4а): compile each schema via `json-schema-to-typescript`.
	console.log(
		`[generate-types] found ${ entries.length } schema(s); compilation lands in Крок 4а.`
	);
}

const watch = process.argv.includes( '--watch' );

await generate();

if ( watch ) {
	const { watch: fsWatch } = await import( 'node:fs' );
	if ( existsSync( schemasDir ) ) {
		console.log( '[generate-types] watching schemas/ …' );
		fsWatch( schemasDir, { recursive: true }, () => {
			generate().catch( ( err ) => console.error( err ) );
		} );
	}
}
