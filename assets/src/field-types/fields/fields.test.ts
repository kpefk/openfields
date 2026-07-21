/**
 * Tests for the built-in field-type definitions (metadata and validation).
 */

import { textField } from './Text';
import { numberField } from './Number';
import { trueFalseField } from './TrueFalse';
import type { FieldConfig } from '../types';

function config( overrides: Partial< FieldConfig > = {} ): FieldConfig {
	return {
		key: 'field_test',
		name: 'test',
		label: 'Test',
		type: 'text',
		...overrides,
	};
}

describe( 'textField', () => {
	it( 'exposes its metadata', () => {
		expect( textField.type ).toBe( 'text' );
		expect( textField.category ).toBe( 'basic' );
		expect( textField.getDefaultConfig() ).toEqual( { defaultValue: '' } );
	} );

	it( 'validates the required rule', () => {
		expect(
			textField.validate?.( '', config( { required: true } ) )
		).toEqual( expect.any( String ) );
		expect(
			textField.validate?.( 'hello', config( { required: true } ) )
		).toBeNull();
		expect( textField.validate?.( '', config() ) ).toBeNull();
	} );
} );

describe( 'numberField', () => {
	it( 'exposes its metadata', () => {
		expect( numberField.type ).toBe( 'number' );
		expect( numberField.getDefaultConfig() ).toEqual( {
			defaultValue: null,
		} );
	} );

	it( 'validates required and NaN', () => {
		expect(
			numberField.validate?.( null, config( { required: true } ) )
		).toEqual( expect.any( String ) );
		expect( numberField.validate?.( Number.NaN, config() ) ).toEqual(
			expect.any( String )
		);
		expect(
			numberField.validate?.( 5, config( { required: true } ) )
		).toBeNull();
		expect( numberField.validate?.( null, config() ) ).toBeNull();
	} );
} );

describe( 'trueFalseField', () => {
	it( 'exposes its metadata and defaults to false', () => {
		expect( trueFalseField.type ).toBe( 'true_false' );
		expect( trueFalseField.category ).toBe( 'choice' );
		expect( trueFalseField.getDefaultConfig() ).toEqual( {
			defaultValue: false,
		} );
	} );
} );
