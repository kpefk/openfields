/**
 * Field Group Builder — sortable list of fields.
 */

import {
	DndContext,
	closestCenter,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
	type DragEndEvent,
} from '@dnd-kit/core';
import {
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { __ } from '@wordpress/i18n';
import type { Dispatch } from 'react';
import type { BuilderAction, FieldGroupState } from './state';
import { getFieldType } from '../../field-types/registry';
import { uniqueKey } from './util';
import { SortableFieldRow } from './SortableFieldRow';
import { FieldSettings } from './FieldSettings';

interface Props {
	state: FieldGroupState;
	dispatch: Dispatch< BuilderAction >;
}

export function FieldList( { state, dispatch }: Props ) {
	const sensors = useSensors(
		useSensor( PointerSensor ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const onDragEnd = ( event: DragEndEvent ) => {
		const { active, over } = event;
		if ( ! over || active.id === over.id ) {
			return;
		}
		const from = state.fields.findIndex( ( f ) => f.key === active.id );
		const to = state.fields.findIndex( ( f ) => f.key === over.id );
		dispatch( { type: 'MOVE_FIELD', from, to } );
	};

	if ( state.fields.length === 0 ) {
		return (
			<p className="openfields-field-list__empty">
				{ __(
					'No fields yet. Add your first field below.',
					'openfields'
				) }
			</p>
		);
	}

	return (
		<DndContext
			sensors={ sensors }
			collisionDetection={ closestCenter }
			onDragEnd={ onDragEnd }
		>
			<SortableContext
				items={ state.fields.map( ( field ) => field.key ) }
				strategy={ verticalListSortingStrategy }
			>
				<ul className="openfields-field-list">
					{ state.fields.map( ( field ) => {
						const definition = getFieldType( field.type );
						return (
							<SortableFieldRow
								key={ field.key }
								field={ field }
								typeLabel={ definition?.label ?? field.type }
								selected={
									state.selectedFieldKey === field.key
								}
								onSelect={ () =>
									dispatch( {
										type: 'SELECT_FIELD',
										key:
											state.selectedFieldKey === field.key
												? null
												: field.key,
									} )
								}
								onDuplicate={ () =>
									dispatch( {
										type: 'DUPLICATE_FIELD',
										key: field.key,
										newKey: uniqueKey(),
									} )
								}
								onRemove={ () =>
									dispatch( {
										type: 'REMOVE_FIELD',
										key: field.key,
									} )
								}
							>
								<FieldSettings
									field={ field }
									allFields={ state.fields }
									dispatch={ dispatch }
								/>
							</SortableFieldRow>
						);
					} ) }
				</ul>
			</SortableContext>
		</DndContext>
	);
}
