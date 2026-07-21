/**
 * Tests for the additional built-in field-type definitions.
 */

import { textareaField } from './Textarea';
import { emailField } from './Email';
import { urlField } from './Url';
import { imageField } from './Image';
import { selectField } from './Select';
import { checkboxField } from './Checkbox';
import { radioField } from './Radio';
import { messageField } from './Message';
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

describe( 'emailField', () => {
	it( 'rejects malformed addresses but allows valid ones', () => {
		expect( emailField.validate?.( 'nope', config() ) ).toEqual(
			expect.any( String )
		);
		expect(
			emailField.validate?.( 'user@example.com', config() )
		).toBeNull();
		expect( emailField.validate?.( '', config() ) ).toBeNull();
	} );

	it( 'enforces required', () => {
		expect(
			emailField.validate?.( '', config( { required: true } ) )
		).toEqual( expect.any( String ) );
	} );
} );

describe( 'urlField', () => {
	it( 'rejects malformed URLs but allows valid ones', () => {
		expect( urlField.validate?.( 'not a url', config() ) ).toEqual(
			expect.any( String )
		);
		expect(
			urlField.validate?.( 'https://example.com', config() )
		).toBeNull();
	} );
} );

describe( 'required across field types', () => {
	it( 'flags empty required values', () => {
		expect(
			textareaField.validate?.( '', config( { required: true } ) )
		).toEqual( expect.any( String ) );
		expect(
			imageField.validate?.( null, config( { required: true } ) )
		).toEqual( expect.any( String ) );
		expect(
			selectField.validate?.( '', config( { required: true } ) )
		).toEqual( expect.any( String ) );
		expect(
			checkboxField.validate?.( [], config( { required: true } ) )
		).toEqual( expect.any( String ) );
		expect(
			radioField.validate?.( '', config( { required: true } ) )
		).toEqual( expect.any( String ) );
	} );

	it( 'passes non-empty required values', () => {
		expect(
			checkboxField.validate?.( [ 'a' ], config( { required: true } ) )
		).toBeNull();
		expect(
			imageField.validate?.( 5, config( { required: true } ) )
		).toBeNull();
	} );
} );

describe( 'field metadata', () => {
	it( 'exposes categories', () => {
		expect( imageField.category ).toBe( 'content' );
		expect( selectField.category ).toBe( 'choice' );
		expect( messageField.category ).toBe( 'layout' );
	} );

	it( 'message has no validator', () => {
		expect( messageField.validate ).toBeUndefined();
	} );
} );
