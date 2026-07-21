/**
 * Image field type (client).
 */

import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';
import { MediaField } from './MediaField';

function ImageEdit( props: FieldEditProps< number | null > ) {
	return (
		<MediaField
			config={ props.config }
			value={ props.value }
			onChange={ props.onChange }
			disabled={ props.disabled }
			allowedTypes={ [ 'image' ] }
			selectLabel={ __( 'Select image', 'openfields' ) }
		/>
	);
}

export const imageField: FieldTypeDefinition< number | null > = {
	type: 'image',
	label: __( 'Image', 'openfields' ),
	category: 'content',
	EditComponent: ImageEdit,
	getDefaultConfig: () => ( { defaultValue: null } ),
	validate: ( value, config ) => requiredError( value, config ),
};
