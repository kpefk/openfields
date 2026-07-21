/**
 * Shared client-side validation helpers for field types.
 */

import { __ } from '@wordpress/i18n';
import type { FieldConfig } from './types';

/**
 * Whether a value is considered empty for the required check.
 */
export function isBlank( value: unknown ): boolean {
	return (
		value === null ||
		value === undefined ||
		value === '' ||
		( Array.isArray( value ) && value.length === 0 )
	);
}

/**
 * Return the "required" error message when a required field is empty.
 */
export function requiredError(
	value: unknown,
	config: FieldConfig
): string | null {
	return config.required && isBlank( value )
		? __( 'This field is required.', 'openfields' )
		: null;
}
