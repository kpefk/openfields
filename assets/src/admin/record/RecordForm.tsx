/**
 * Record form — renders a field group's fields for a post, reusing each field
 * type's edit component. Applies conditional-logic visibility and surfaces
 * validation errors (client-side for touched fields, plus any from the server).
 */

import { useState, useEffect } from '@wordpress/element';
import type { ComponentType, CSSProperties } from 'react';
import { getFieldType } from '../../field-types/registry';
import type { FieldConfig } from '../../field-types/types';
import { isFieldVisible, type RecordValues } from './conditional';

interface LooseEditProps {
	config: FieldConfig;
	value: unknown;
	onChange: ( value: unknown ) => void;
	disabled?: boolean;
}

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
		<div className="openfields-record-fields">
			{ fields.map( ( field ) => {
				if ( ! isFieldVisible( field, values, fields ) ) {
					return null;
				}

				const definition = getFieldType( field.type );
				if ( ! definition ) {
					return null;
				}

				const Edit =
					definition.EditComponent as ComponentType< LooseEditProps >;

				const style: CSSProperties | undefined = field.wrapper?.width
					? { width: field.wrapper.width }
					: undefined;

				const error = errorFor( field );

				return (
					<div
						key={ field.key }
						className={
							'openfields-record-field' +
							( error ? ' openfields-record-field--error' : '' )
						}
						style={ style }
					>
						<Edit
							config={ field }
							value={ values[ field.name ] }
							onChange={ ( value ) =>
								setValue( field.name, value )
							}
						/>
						{ error ? (
							<p
								className="openfields-record-field__error"
								role="alert"
							>
								{ error }
							</p>
						) : null }
					</div>
				);
			} ) }
		</div>
	);
}
