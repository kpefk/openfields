/**
 * Tests for the core field-type registration.
 */

import {
	registerCoreFieldTypes,
	getFieldTypes,
	clearFieldTypes,
} from './index';

describe( 'registerCoreFieldTypes', () => {
	it( 'registers all 13 core types', () => {
		clearFieldTypes();
		registerCoreFieldTypes();

		const types = getFieldTypes().map( ( def ) => def.type );

		expect( types ).toHaveLength( 13 );
		expect( types ).toContain( 'text' );
		expect( types ).toContain( 'wysiwyg' );
		expect( types ).toContain( 'message' );
	} );
} );
