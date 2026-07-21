/**
 * True/False field type (client).
 */

import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';

function TrueFalseEdit( {
	config,
	value,
	onChange,
	disabled,
}: FieldEditProps< boolean > ) {
	return (
		<ToggleControl
			label={ config.label }
			help={ config.instructions }
			checked={ Boolean( value ) }
			disabled={ disabled }
			onChange={ onChange }
			__nextHasNoMarginBottom
		/>
	);
}

export const trueFalseField: FieldTypeDefinition< boolean > = {
	type: 'true_false',
	label: __( 'True / False', 'openfields' ),
	category: 'choice',
	EditComponent: TrueFalseEdit,
	getDefaultConfig: () => ( { defaultValue: false } ),
};
