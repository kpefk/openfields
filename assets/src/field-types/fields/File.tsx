/**
 * File field type (client).
 */

import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';
import { MediaField } from './MediaField';

function FileEdit( props: FieldEditProps< number | null > ) {
	return (
		<MediaField
			config={ props.config }
			value={ props.value }
			onChange={ props.onChange }
			disabled={ props.disabled }
			allowedTypes={ [] }
			selectLabel={ __( 'Select file', 'openfields' ) }
		/>
	);
}

export const fileField: FieldTypeDefinition< number | null > = {
	type: 'file',
	label: __( 'File', 'openfields' ),
	category: 'content',
	EditComponent: FileEdit,
	getDefaultConfig: () => ( { defaultValue: null } ),
	validate: ( value, config ) => requiredError( value, config ),
};
