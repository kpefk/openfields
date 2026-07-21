/**
 * Field Group Builder — settings panel for a single field.
 */

import {
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { Dispatch } from 'react';
import type { BuilderAction } from './state';
import type { FieldConfig } from '../../field-types/types';
import { ConditionalLogicEditor } from './ConditionalLogicEditor';

interface Props {
	field: FieldConfig;
	allFields: FieldConfig[];
	dispatch: Dispatch< BuilderAction >;
}

export function FieldSettings( { field, allFields, dispatch }: Props ) {
	const update = ( changes: Partial< FieldConfig > ) =>
		dispatch( { type: 'UPDATE_FIELD', key: field.key, changes } );

	return (
		<div className="openfields-field-settings">
			<TextControl
				label={ __( 'Label', 'openfields' ) }
				value={ field.label }
				onChange={ ( label ) => update( { label } ) }
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<TextControl
				label={ __( 'Name', 'openfields' ) }
				help={ __(
					'The key used to store and retrieve the value.',
					'openfields'
				) }
				value={ field.name }
				onChange={ ( name ) => update( { name } ) }
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<TextareaControl
				label={ __( 'Instructions', 'openfields' ) }
				value={ field.instructions ?? '' }
				onChange={ ( instructions ) => update( { instructions } ) }
				__nextHasNoMarginBottom
			/>
			<ToggleControl
				label={ __( 'Required', 'openfields' ) }
				checked={ Boolean( field.required ) }
				onChange={ ( required ) => update( { required } ) }
				__nextHasNoMarginBottom
			/>
			<ConditionalLogicEditor
				field={ field }
				allFields={ allFields }
				onChange={ ( conditionalLogic ) =>
					update( { conditionalLogic } )
				}
			/>
		</div>
	);
}
