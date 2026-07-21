/**
 * Select field type (client).
 */

import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';
import { choiceOptions } from './choices';

type SelectValue = string | string[];

function SelectEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< SelectValue > ) {
	const options = choiceOptions( config );
	const multiple = Boolean( config.settings?.multiple );

	if ( multiple ) {
		const current = Array.isArray( value ) ? value : [];
		return (
			<SelectControl
				multiple
				label={ config.label }
				help={ config.instructions }
				value={ current }
				options={ options }
				disabled={ disabled }
				onChange={ ( next ) => onChange( next ) }
				__nextHasNoMarginBottom
			/>
		);
	}

	const current = typeof value === 'string' ? value : '';
	return (
		<SelectControl
			label={ config.label }
			help={ config.instructions }
			value={ current }
			options={ options }
			disabled={ disabled }
			onChange={ ( next ) => onChange( next ) }
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);
}

export const selectField: FieldTypeDefinition< SelectValue > = {
	type: 'select',
	label: __( 'Select', 'openfields' ),
	category: 'choice',
	EditComponent: SelectEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) => requiredError( value, config ),
};
