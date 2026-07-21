/**
 * WYSIWYG field type (client).
 *
 * Milestone note: this uses a plain HTML textarea. A full rich-text editor
 * (wp.editor / TinyMCE) integration is a later enhancement; the stored HTML is
 * sanitized server-side with wp_kses_post().
 */

import { TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';
import { requiredError } from '../validation';

function WysiwygEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< string > ) {
	return (
		<TextareaControl
			label={ config.label }
			help={
				config.instructions ?? __( 'HTML is allowed.', 'openfields' )
			}
			value={ value ?? '' }
			rows={ 8 }
			disabled={ disabled }
			onChange={ onChange }
			__nextHasNoMarginBottom
		/>
	);
}

export const wysiwygField: FieldTypeDefinition< string > = {
	type: 'wysiwyg',
	label: __( 'WYSIWYG Editor', 'openfields' ),
	category: 'content',
	EditComponent: WysiwygEdit,
	getDefaultConfig: () => ( { defaultValue: '' } ),
	validate: ( value, config ) => requiredError( value, config ),
};
