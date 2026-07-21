/**
 * Record form — renders a field group's fields for a post, reusing each field
 * type's edit component.
 */

import { useState, useEffect } from '@wordpress/element';
import type { ComponentType, CSSProperties } from 'react';
import { getFieldType } from '../../field-types/registry';
import type { FieldConfig } from '../../field-types/types';

type RecordValues = Record< string, unknown >;

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
}

export function RecordForm( { fields, initialValues, onChange }: Props ) {
	const [ values, setValues ] = useState< RecordValues >( initialValues );

	useEffect( () => {
		onChange( values );
	}, [ values, onChange ] );

	const setValue = ( name: string, value: unknown ) =>
		setValues( ( previous ) => ( { ...previous, [ name ]: value } ) );

	return (
		<div className="openfields-record-fields">
			{ fields.map( ( field ) => {
				const definition = getFieldType( field.type );
				if ( ! definition ) {
					return null;
				}

				const Edit =
					definition.EditComponent as ComponentType< LooseEditProps >;

				const style: CSSProperties | undefined = field.wrapper?.width
					? { width: field.wrapper.width }
					: undefined;

				return (
					<div
						key={ field.key }
						className="openfields-record-field"
						style={ style }
					>
						<Edit
							config={ field }
							value={ values[ field.name ] }
							onChange={ ( value ) =>
								setValue( field.name, value )
							}
						/>
					</div>
				);
			} ) }
		</div>
	);
}
