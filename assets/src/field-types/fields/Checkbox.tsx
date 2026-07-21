/**
 * Checkbox field type (client).
 */

import { BaseControl, CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';
import { choiceOptions } from './choices';

function CheckboxEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< string[] > ) {
	const current = Array.isArray( value ) ? value : [];
	const options = choiceOptions( config );

	const toggle = ( option: string, checked: boolean ) => {
		onChange(
			checked
				? [ ...current, option ]
				: current.filter( ( item ) => item !== option )
		);
	};

	return (
		<BaseControl
			id={ `openfields-checkbox-${ config.key }` }
			label={ config.label }
			help={ config.instructions }
			__nextHasNoMarginBottom
		>
			{ options.map( ( option ) => (
				<CheckboxControl
					key={ option.value }
					label={ option.label }
					checked={ current.includes( option.value ) }
					disabled={ disabled }
					onChange={ ( checked ) => toggle( option.value, checked ) }
					__nextHasNoMarginBottom
				/>
			) ) }
		</BaseControl>
	);
}

export const checkboxField: FieldTypeDefinition< string[] > = {
	type: 'checkbox',
	label: __( 'Checkbox', 'openfields' ),
	category: 'choice',
	EditComponent: CheckboxEdit,
	getDefaultConfig: () => ( { defaultValue: [] } ),
	validate: ( value, config ) => requiredError( value, config ),
};
