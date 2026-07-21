/**
 * Email field type (client).
 */

import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function EmailEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< string > ) {
	return (
		<TextControl
			type="email"
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

export const emailField: FieldTypeDefinition< string > = {
	type: 'email',
	label: __( 'Email', 'openfields' ),
	category: 'basic',
	EditComponent: EmailEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) => {
		const required = requiredError( value, config );
		if ( required ) {
			return required;
		}
		if ( value && ! EMAIL_PATTERN.test( value ) ) {
			return __( 'Please enter a valid email address.', 'openfields' );
		}
		return null;
	},
};
