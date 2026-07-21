/**
 * Radio button field type (client).
 */

import { RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';
import { choiceOptions } from './choices';

function RadioEdit( { config, value, onChange }: FieldEditProps< string > ) {
	return (
		<RadioControl
			label={ config.label }
			help={ config.instructions }
			selected={ value ?? '' }
			options={ choiceOptions( config ) }
			onChange={ onChange }
		/>
	);
}

export const radioField: FieldTypeDefinition< string > = {
	type: 'radio',
	label: __( 'Radio Button', 'openfields' ),
	category: 'choice',
	EditComponent: RadioEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) => requiredError( value, config ),
};
