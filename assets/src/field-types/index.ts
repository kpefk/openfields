/**
 * Field Type API — public entry point.
 *
 * Re-exports the contract and registry, and registers the built-in field types.
 */

export * from './types';
export * from './registry';

import { registerFieldType } from './registry';
import { textField } from './fields/Text';
import { numberField } from './fields/Number';
import { trueFalseField } from './fields/TrueFalse';

/**
 * Register the built-in (core) field types.
 */
export function registerCoreFieldTypes(): void {
	registerFieldType( textField );
	registerFieldType( numberField );
	registerFieldType( trueFalseField );
}
