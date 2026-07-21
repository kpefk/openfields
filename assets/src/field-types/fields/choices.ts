/**
 * Helper to read a choice field's options from its configuration.
 */

import type { FieldConfig } from '../types';

export interface ChoiceOption {
	value: string;
	label: string;
}

/**
 * Extract `{ value, label }` options from a field's `settings.choices`
 * (a value → label map).
 */
export function choiceOptions( config: FieldConfig ): ChoiceOption[] {
	const choices = config.settings?.choices;

	if ( ! choices || typeof choices !== 'object' ) {
		return [];
	}

	return Object.entries( choices as Record< string, unknown > ).map(
		( [ value, label ] ) => ( { value, label: String( label ) } )
	);
}
