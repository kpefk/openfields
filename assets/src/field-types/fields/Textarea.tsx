/**
 * Textarea field type (client).
 */

import { TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';

function TextareaEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< string > ) {
	return (
		<TextareaControl
			label={ config.label }
			help={ config.instructions }
			placeholder={ config.placeholder }
			value={ value ?? '' }
			disabled={ disabled }
			onChange={ onChange }
			__nextHasNoMarginBottom
		/>
	);
}

export const textareaField: FieldTypeDefinition< string > = {
	type: 'textarea',
	label: __( 'Textarea', 'openfields' ),
	category: 'basic',
	EditComponent: TextareaEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) => requiredError( value, config ),
};
