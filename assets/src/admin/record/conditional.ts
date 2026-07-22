/**
 * Client-side conditional logic evaluation for the record form.
 *
 * A field is visible when its `conditionalLogic` is disabled/empty, or when any
 * of its OR-groups fully matches (each rule in a group is ANDed). Rules
 * reference a dependency field by its key; values are keyed by field name, so
 * the dependency's name is resolved through the field list.
 */

import type {
	ConditionalLogicRule,
	FieldConfig,
} from '../../field-types/types';

export type RecordValues = Record< string, unknown >;

function isEmpty( value: unknown ): boolean {
	return (
		value === null ||
		value === undefined ||
		value === '' ||
		( Array.isArray( value ) && value.length === 0 )
	);
}

function ruleMatches(
	rule: ConditionalLogicRule,
	values: RecordValues,
	fieldsByKey: Map< string, FieldConfig >
): boolean {
	const dependency = fieldsByKey.get( rule.field );
	const name = dependency?.name ?? rule.field;
	const actual = values[ name ];
	const expected = rule.value;

	switch ( rule.operator ) {
		case '==':
			return String( actual ?? '' ) === String( expected ?? '' );
		case '!=':
			return String( actual ?? '' ) !== String( expected ?? '' );
		case '>':
			return Number( actual ) > Number( expected );
		case '<':
			return Number( actual ) < Number( expected );
		case 'contains':
			return String( actual ?? '' ).includes( String( expected ?? '' ) );
		case 'empty':
			return isEmpty( actual );
		case 'not_empty':
			return ! isEmpty( actual );
		case 'matches':
			try {
				return new RegExp( String( expected ?? '' ) ).test(
					String( actual ?? '' )
				);
			} catch {
				return false;
			}
		default:
			return false;
	}
}

/**
 * Whether a field should be visible given the current record values.
 */
export function isFieldVisible(
	field: FieldConfig,
	values: RecordValues,
	fields: FieldConfig[]
): boolean {
	const logic = field.conditionalLogic;

	if ( ! Array.isArray( logic ) || logic.length === 0 ) {
		return true;
	}

	const fieldsByKey = new Map(
		fields.map( ( item ): [ string, FieldConfig ] => [ item.key, item ] )
	);

	return logic.some( ( group ) =>
		group.every( ( rule ) => ruleMatches( rule, values, fieldsByKey ) )
	);
}
