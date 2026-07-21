/**
 * Field Type API — TypeScript contract.
 *
 * The serializable field configuration (`FieldConfig` and friends) is generated
 * from `schemas/field-config.schema.json`, the single source of truth shared
 * with the PHP layer. The React-facing interfaces below cannot be expressed as
 * JSON Schema (they reference component types), so they live here.
 */

import type { ComponentType, ReactNode } from 'react';
import type {
	FieldConfig,
	ConditionalLogicGroup,
	ConditionalLogicRule,
	WrapperConfig,
} from './generated/field-config';

export type {
	FieldConfig,
	ConditionalLogicGroup,
	ConditionalLogicRule,
	WrapperConfig,
};

/**
 * Fallback value shape for a field whose value type is not specialised.
 */
export interface FieldValue {
	[key: string]: unknown;
}

/**
 * Builder category a field type belongs to.
 */
export type FieldCategory =
	| 'basic'
	| 'content'
	| 'choice'
	| 'relational'
	| 'layout';

/**
 * Props passed to a field's edit component in the record form.
 */
export interface FieldEditProps< TValue = FieldValue > {
	config: FieldConfig;
	value: TValue;
	onChange: ( value: TValue ) => void;
	disabled?: boolean;
}

/**
 * Props passed to a field's settings component in the group builder.
 */
export interface FieldSettingsProps {
	config: FieldConfig;
	onChange: ( config: FieldConfig ) => void;
}

/**
 * The definition a field type registers with {@link registerFieldType}.
 */
export interface FieldTypeDefinition< TValue = FieldValue > {
	/** Unique type identifier, e.g. "text". */
	type: string;

	/** Display name in the field-type picker. */
	label: string;

	/** Dashicon slug or an SVG element. */
	icon?: string | ReactNode;

	/** Category in the field-type picker. */
	category: FieldCategory;

	/** React component rendering the field in the record form. */
	EditComponent: ComponentType< FieldEditProps< TValue > >;

	/** React component rendering the field's settings in the builder. */
	SettingsComponent?: ComponentType< FieldSettingsProps >;

	/** Client-side validation; returns an error string or null. */
	validate?: ( value: TValue, config: FieldConfig ) => string | null;

	/** Default configuration for a new field of this type. */
	getDefaultConfig: () => Partial< FieldConfig >;
}
