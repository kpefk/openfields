/**
 * Field Group Builder — state model and reducer.
 *
 * The reducer is a pure function so it can be unit tested without rendering.
 */

import type { FieldConfig } from '../../field-types/types';

export interface LocationRule {
	param: string;
	operator: '==' | '!=';
	value: string;
}

export type LocationGroup = LocationRule[];

export interface GroupSettings {
	position: 'normal' | 'side' | 'acf_after_title';
	style: 'default' | 'seamless';
	label_placement: 'top' | 'left';
	instruction_placement: 'label' | 'field';
	menu_order: number;
	description: string;
}

export interface FieldGroupState {
	key: string;
	title: string;
	fields: FieldConfig[];
	location: LocationGroup[];
	settings: GroupSettings;
	selectedFieldKey: string | null;
}

/**
 * Loose shape used to seed the builder (e.g. from persisted, possibly partial,
 * configuration).
 */
export interface FieldGroupInput {
	key?: string;
	title?: string;
	fields?: FieldConfig[];
	location?: LocationGroup[];
	settings?: Partial< GroupSettings >;
	selectedFieldKey?: string | null;
}

export const defaultGroupSettings: GroupSettings = {
	position: 'normal',
	style: 'default',
	label_placement: 'top',
	instruction_placement: 'label',
	menu_order: 0,
	description: '',
};

export function createInitialState(
	partial: FieldGroupInput = {}
): FieldGroupState {
	return {
		key: partial.key ?? '',
		title: partial.title ?? '',
		fields: partial.fields ?? [],
		location: partial.location ?? [],
		settings: { ...defaultGroupSettings, ...( partial.settings ?? {} ) },
		selectedFieldKey: partial.selectedFieldKey ?? null,
	};
}

/**
 * Build a blank field of the given type with a caller-supplied unique key.
 */
export function createField( type: string, key: string ): FieldConfig {
	return {
		key,
		name: key,
		label: '',
		type,
	};
}

export type BuilderAction =
	| { type: 'SET_TITLE'; title: string }
	| { type: 'ADD_FIELD'; field: FieldConfig }
	| { type: 'REMOVE_FIELD'; key: string }
	| { type: 'DUPLICATE_FIELD'; key: string; newKey: string }
	| { type: 'MOVE_FIELD'; from: number; to: number }
	| { type: 'UPDATE_FIELD'; key: string; changes: Partial< FieldConfig > }
	| { type: 'SELECT_FIELD'; key: string | null }
	| { type: 'UPDATE_SETTINGS'; changes: Partial< GroupSettings > }
	| { type: 'SET_LOCATION'; location: LocationGroup[] };

function move< T >( items: T[], from: number, to: number ): T[] {
	if (
		from === to ||
		from < 0 ||
		to < 0 ||
		from >= items.length ||
		to >= items.length
	) {
		return items;
	}

	const next = items.slice();
	const [ moved ] = next.splice( from, 1 );
	next.splice( to, 0, moved );
	return next;
}

export function reducer(
	state: FieldGroupState,
	action: BuilderAction
): FieldGroupState {
	switch ( action.type ) {
		case 'SET_TITLE':
			return { ...state, title: action.title };

		case 'ADD_FIELD':
			return {
				...state,
				fields: [ ...state.fields, action.field ],
				selectedFieldKey: action.field.key,
			};

		case 'REMOVE_FIELD': {
			const fields = state.fields.filter(
				( field ) => field.key !== action.key
			);
			return {
				...state,
				fields,
				selectedFieldKey:
					state.selectedFieldKey === action.key
						? null
						: state.selectedFieldKey,
			};
		}

		case 'DUPLICATE_FIELD': {
			const index = state.fields.findIndex(
				( field ) => field.key === action.key
			);
			if ( index === -1 ) {
				return state;
			}
			const original = state.fields[ index ];
			const copy: FieldConfig = {
				...original,
				key: action.newKey,
				name: action.newKey,
			};
			const fields = state.fields.slice();
			fields.splice( index + 1, 0, copy );
			return { ...state, fields, selectedFieldKey: action.newKey };
		}

		case 'MOVE_FIELD':
			return {
				...state,
				fields: move( state.fields, action.from, action.to ),
			};

		case 'UPDATE_FIELD':
			return {
				...state,
				fields: state.fields.map( ( field ) =>
					field.key === action.key
						? { ...field, ...action.changes }
						: field
				),
			};

		case 'SELECT_FIELD':
			return { ...state, selectedFieldKey: action.key };

		case 'UPDATE_SETTINGS':
			return {
				...state,
				settings: { ...state.settings, ...action.changes },
			};

		case 'SET_LOCATION':
			return { ...state, location: action.location };

		default:
			return state;
	}
}

/**
 * Serialize the builder state to the persisted field-group configuration shape.
 */
export function serialize( state: FieldGroupState ): {
	key: string;
	title: string;
	fields: FieldConfig[];
	location: LocationGroup[];
	settings: GroupSettings;
} {
	return {
		key: state.key,
		title: state.title,
		fields: state.fields,
		location: state.location,
		settings: state.settings,
	};
}
