/**
 * URL field type (client).
 */

import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';

function isValidUrl( value: string ): boolean {
	try {
		// eslint-disable-next-line no-new
		new URL( value );
		return true;
	} catch {
		return false;
	}
}

function UrlEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< string > ) {
	return (
		<TextControl
			type="url"
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

export const urlField: FieldTypeDefinition< string > = {
	type: 'url',
	label: __( 'URL', 'openfields' ),
	category: 'basic',
	EditComponent: UrlEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) => {
		const required = requiredError( value, config );
		if ( required ) {
			return required;
		}
		if ( value && ! isValidUrl( value ) ) {
			return __( 'Please enter a valid URL.', 'openfields' );
		}
		return null;
	},
};
