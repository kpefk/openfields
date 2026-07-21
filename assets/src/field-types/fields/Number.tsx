/**
 * Number field type (client).
 */

import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';

type NumberValue = number | null;

function NumberEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< NumberValue > ) {
	return (
		<TextControl
			type="number"
			label={ config.label }
			help={ config.instructions }
			placeholder={ config.placeholder }
			value={ value === null || value === undefined ? '' : String( value ) }
			disabled={ disabled }
			onChange={ ( next ) =>
				onChange( '' === next ? null : Number( next ) )
			}
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);
}

export const numberField: FieldTypeDefinition< NumberValue > = {
	type: 'number',
	label: __( 'Number', 'openfields' ),
	category: 'basic',
	EditComponent: NumberEdit,
	getDefaultConfig: () => ( { defaultValue: null } ),
	validate: ( value, config ) => {
		if ( config.required && ( value === null || value === undefined ) ) {
			return __( 'This field is required.', 'openfields' );
		}

		if ( value !== null && value !== undefined && Number.isNaN( value ) ) {
			return __( 'Please enter a valid number.', 'openfields' );
		}

		return null;
	},
};
