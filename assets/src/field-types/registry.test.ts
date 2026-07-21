/**
 * Tests for the field-type registry.
 */

import {
	registerFieldType,
	getFieldType,
	hasFieldType,
	getFieldTypes,
	clearFieldTypes,
} from './registry';
import type { FieldTypeDefinition } from './types';

function makeType( type: string ): FieldTypeDefinition {
	return {
		type,
		label: type,
		category: 'basic',
		EditComponent: () => null,
		getDefaultConfig: () => ( {} ),
	};
}

describe( 'field-type registry', () => {
	beforeEach( () => clearFieldTypes() );

	it( 'registers and retrieves a type', () => {
		const text = makeType( 'text' );
		registerFieldType( text );

		expect( hasFieldType( 'text' ) ).toBe( true );
		expect( getFieldType( 'text' ) ).toBe( text );
		expect( getFieldType( 'missing' ) ).toBeUndefined();
	} );

	it( 'lists registered types in registration order', () => {
		registerFieldType( makeType( 'text' ) );
		registerFieldType( makeType( 'number' ) );

		expect( getFieldTypes().map( ( def ) => def.type ) ).toEqual( [
			'text',
			'number',
		] );
	} );

	it( 'replaces a type registered under the same identifier', () => {
		registerFieldType( makeType( 'text' ) );
		const replacement = makeType( 'text' );
		registerFieldType( replacement );

		expect( getFieldTypes() ).toHaveLength( 1 );
		expect( getFieldType( 'text' ) ).toBe( replacement );
	} );
} );
