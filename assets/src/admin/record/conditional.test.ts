/**
 * Tests for record-form conditional logic.
 */

import { isFieldVisible } from './conditional';
import type { FieldConfig } from '../../field-types/types';

function field( overrides: Partial< FieldConfig > ): FieldConfig {
	return {
		key: 'field_x',
		name: 'x',
		label: 'X',
		type: 'text',
		...overrides,
	};
}

const controller = field( { key: 'field_a', name: 'a', label: 'A' } );

describe( 'isFieldVisible', () => {
	it( 'is visible without conditional logic', () => {
		const target = field( { key: 'field_b', name: 'b' } );
		expect( isFieldVisible( target, {}, [ controller, target ] ) ).toBe(
			true
		);
	} );

	it( 'hides until an equals rule matches', () => {
		const target = field( {
			key: 'field_b',
			name: 'b',
			conditionalLogic: [
				[ { field: 'field_a', operator: '==', value: 'yes' } ],
			],
		} );
		const fields = [ controller, target ];

		expect( isFieldVisible( target, { a: 'no' }, fields ) ).toBe( false );
		expect( isFieldVisible( target, { a: 'yes' }, fields ) ).toBe( true );
	} );

	it( 'supports not_empty', () => {
		const target = field( {
			key: 'field_b',
			name: 'b',
			conditionalLogic: [
				[ { field: 'field_a', operator: 'not_empty' } ],
			],
		} );
		const fields = [ controller, target ];

		expect( isFieldVisible( target, { a: '' }, fields ) ).toBe( false );
		expect( isFieldVisible( target, { a: 'x' }, fields ) ).toBe( true );
	} );

	it( 'ANDs rules within a group and ORs across groups', () => {
		const other = field( { key: 'field_c', name: 'c' } );
		const target = field( {
			key: 'field_b',
			name: 'b',
			conditionalLogic: [
				[
					{ field: 'field_a', operator: '==', value: '1' },
					{ field: 'field_c', operator: '==', value: '2' },
				],
				[ { field: 'field_a', operator: '==', value: '9' } ],
			],
		} );
		const fields = [ controller, other, target ];

		// First group needs both; second group is an alternative.
		expect( isFieldVisible( target, { a: '1', c: '2' }, fields ) ).toBe(
			true
		);
		expect( isFieldVisible( target, { a: '1', c: '0' }, fields ) ).toBe(
			false
		);
		expect( isFieldVisible( target, { a: '9' }, fields ) ).toBe( true );
	} );
} );
