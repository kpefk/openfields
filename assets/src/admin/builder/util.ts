/**
 * Field Group Builder — small helpers.
 */

/**
 * Generate a reasonably unique field/group key, e.g. "field_a1b2c3d4".
 */
export function uniqueKey( prefix = 'field' ): string {
	const random = Math.random().toString( 36 ).slice( 2, 10 );
	return `${ prefix }_${ random }`;
}
