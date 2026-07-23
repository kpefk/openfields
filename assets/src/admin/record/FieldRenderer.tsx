/**
 * Controlled renderer for a set of fields: applies conditional-logic
 * visibility, renders each field via its type's edit component, and shows an
 * optional error. Shared by the classic record form and the Gutenberg sidebar.
 */

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
	values: RecordValues;
	onChange: ( name: string, value: unknown ) => void;
	errorFor?: ( field: FieldConfig ) => string | undefined;
}

export function FieldRenderer( { fields, values, onChange, errorFor }: Props ) {
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

				const error = errorFor?.( field );

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
								onChange( field.name, value )
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
