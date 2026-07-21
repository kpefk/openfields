/**
 * Text field type (client).
 */

import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';

function TextEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< string > ) {
	return (
		<TextControl
			label={ config.label }
			help={ config.instructions }
			placeholder={ config.placeholder }
			value={ value ?? '' }
			disabled={ disabled }
			onChange={ onChange }
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);
}

export const textField: FieldTypeDefinition< string > = {
	type: 'text',
	label: __( 'Text', 'openfields' ),
	category: 'basic',
	EditComponent: TextEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) =>
		config.required && ! value
			? __( 'This field is required.', 'openfields' )
			: null,
};
