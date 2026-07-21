/**
 * Tests for the Field Group Builder reducer.
 */

import {
	reducer,
	createInitialState,
	createField,
	serialize,
	type FieldGroupState,
} from './state';

function stateWithFields( ...types: string[] ): FieldGroupState {
	return createInitialState( {
		fields: types.map( ( type, index ) =>
			createField( type, `field_${ index }` )
		),
	} );
}

describe( 'builder reducer', () => {
	it( 'sets the title', () => {
		const next = reducer( createInitialState(), {
			type: 'SET_TITLE',
			title: 'Hero',
		} );
		expect( next.title ).toBe( 'Hero' );
	} );

	it( 'adds a field and selects it', () => {
		const field = createField( 'text', 'field_a' );
		const next = reducer( createInitialState(), {
			type: 'ADD_FIELD',
			field,
		} );
		expect( next.fields ).toHaveLength( 1 );
		expect( next.selectedFieldKey ).toBe( 'field_a' );
	} );

	it( 'removes a field and clears the selection', () => {
		const state = {
			...stateWithFields( 'text' ),
			selectedFieldKey: 'field_0',
		};
		const next = reducer( state, {
			type: 'REMOVE_FIELD',
			key: 'field_0',
		} );
		expect( next.fields ).toHaveLength( 0 );
		expect( next.selectedFieldKey ).toBeNull();
	} );

	it( 'duplicates a field after the original', () => {
		const next = reducer( stateWithFields( 'text', 'number' ), {
			type: 'DUPLICATE_FIELD',
			key: 'field_0',
			newKey: 'field_copy',
		} );
		expect( next.fields.map( ( f ) => f.key ) ).toEqual( [
			'field_0',
			'field_copy',
			'field_1',
		] );
		expect( next.fields[ 1 ].name ).toBe( 'field_copy' );
	} );

	it( 'moves a field', () => {
		const next = reducer( stateWithFields( 'a', 'b', 'c' ), {
			type: 'MOVE_FIELD',
			from: 0,
			to: 2,
		} );
		expect( next.fields.map( ( f ) => f.key ) ).toEqual( [
			'field_1',
			'field_2',
			'field_0',
		] );
	} );

	it( 'ignores an out-of-range move', () => {
		const state = stateWithFields( 'a', 'b' );
		const next = reducer( state, { type: 'MOVE_FIELD', from: 0, to: 5 } );
		expect( next.fields ).toBe( state.fields );
	} );

	it( 'updates a field', () => {
		const next = reducer( stateWithFields( 'text' ), {
			type: 'UPDATE_FIELD',
			key: 'field_0',
			changes: { label: 'Title', required: true },
		} );
		expect( next.fields[ 0 ].label ).toBe( 'Title' );
		expect( next.fields[ 0 ].required ).toBe( true );
	} );

	it( 'updates group settings', () => {
		const next = reducer( createInitialState(), {
			type: 'UPDATE_SETTINGS',
			changes: { position: 'side' },
		} );
		expect( next.settings.position ).toBe( 'side' );
		expect( next.settings.style ).toBe( 'default' );
	} );

	it( 'replaces the location rules', () => {
		const location = [
			[ { param: 'post_type', operator: '==' as const, value: 'post' } ],
		];
		const next = reducer( createInitialState(), {
			type: 'SET_LOCATION',
			location,
		} );
		expect( next.location ).toEqual( location );
	} );

	it( 'serializes without the editor-only selection', () => {
		const serialized = serialize( {
			...stateWithFields( 'text' ),
			title: 'Group',
			selectedFieldKey: 'field_0',
		} );
		expect( serialized ).not.toHaveProperty( 'selectedFieldKey' );
		expect( serialized.title ).toBe( 'Group' );
		expect( serialized.fields ).toHaveLength( 1 );
	} );
} );
