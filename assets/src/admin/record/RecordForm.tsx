/**
 * Classic-editor record form — owns field values and touched state, mirrors
 * values into a hidden input, and surfaces validation errors (client-side for
 * touched fields, plus any from the server).
 */

import { useState, useEffect } from '@wordpress/element';
import { getFieldType } from '../../field-types/registry';
import type { FieldConfig } from '../../field-types/types';
import type { RecordValues } from './conditional';
import { FieldRenderer } from './FieldRenderer';

interface Props {
	fields: FieldConfig[];
	initialValues: RecordValues;
	onChange: ( values: RecordValues ) => void;
	/** Server-side validation errors keyed by field key. */
	serverErrors?: Record< string, string >;
}

export function RecordForm( {
	fields,
	initialValues,
	onChange,
	serverErrors = {},
}: Props ) {
	const [ values, setValues ] = useState< RecordValues >( initialValues );
	const [ touched, setTouched ] = useState< Record< string, boolean > >( {} );

	useEffect( () => {
		onChange( values );
	}, [ values, onChange ] );

	const setValue = ( name: string, value: unknown ) => {
		setValues( ( previous ) => ( { ...previous, [ name ]: value } ) );
		setTouched( ( previous ) => ( { ...previous, [ name ]: true } ) );
	};

	const errorFor = ( field: FieldConfig ): string | undefined => {
		if ( serverErrors[ field.key ] ) {
			return serverErrors[ field.key ];
		}
		if ( touched[ field.name ] ) {
			const definition = getFieldType( field.type );
			return (
				definition?.validate?.(
					values[ field.name ] as never,
					field
				) ?? undefined
			);
		}
		return undefined;
	};

	return (
		<FieldRenderer
			fields={ fields }
			values={ values }
			onChange={ setValue }
			errorFor={ errorFor }
		/>
	);
}
