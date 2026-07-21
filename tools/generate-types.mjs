/**
 * generate-types — compiles JSON Schemas in `schemas/` into TypeScript files
 * under `assets/src/field-types/generated/`.
 *
 * JSON Schema is the single source of truth shared with the PHP layer
 * (`AbstractFieldType::get_json_schema()`), so the PHP REST contract and the
 * TypeScript front end cannot drift. Pass `--watch` to regenerate on change.
 */

import { readdir, mkdir, writeFile } from 'node:fs/promises';
import { existsSync, watch as fsWatch } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { compileFromFile } from 'json-schema-to-typescript';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const root = path.resolve( __dirname, '..' );
const schemasDir = path.join( root, 'schemas' );
const outDir = path.join( root, 'assets', 'src', 'field-types', 'generated' );

const banner = ( source ) =>
	`/* eslint-disable */\n` +
	`/**\n` +
	` * This file was generated from \`schemas/${ source }\`.\n` +
	` * DO NOT EDIT BY HAND — change the JSON schema and run \`bun run generate:types\`.\n` +
	` */`;

async function generate() {
	if ( ! existsSync( schemasDir ) ) {
		console.log( '[generate-types] no schemas/ directory — skipping.' );
		return;
	}

	const schemas = ( await readdir( schemasDir ) ).filter( ( file ) =>
		file.endsWith( '.schema.json' )
	);

	if ( schemas.length === 0 ) {
		console.log( '[generate-types] no *.schema.json files — skipping.' );
		return;
	}

	await mkdir( outDir, { recursive: true } );

	for ( const schema of schemas ) {
		const ts = await compileFromFile( path.join( schemasDir, schema ), {
			bannerComment: banner( schema ),
			additionalProperties: false,
			cwd: schemasDir,
			style: { useTabs: true, singleQuote: true },
		} );

		const outFile = schema.replace( /\.schema\.json$/, '.ts' );
		await writeFile( path.join( outDir, outFile ), ts );
		console.log( `[generate-types] wrote generated/${ outFile }` );
	}
}

await generate().catch( ( error ) => {
	console.error( '[generate-types] failed:', error );
	process.exit( 1 );
} );

if ( process.argv.includes( '--watch' ) ) {
	console.log( '[generate-types] watching schemas/ …' );
	fsWatch( schemasDir, { recursive: true }, () => {
		generate().catch( ( error ) => console.error( error ) );
	} );
}
