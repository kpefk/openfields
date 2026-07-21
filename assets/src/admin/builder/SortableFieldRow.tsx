/**
 * Field Group Builder — a single sortable field row.
 */

import { Button } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import type { CSSProperties, ReactNode } from 'react';
import type { FieldConfig } from '../../field-types/types';

interface Props {
	field: FieldConfig;
	typeLabel: string;
	selected: boolean;
	onSelect: () => void;
	onDuplicate: () => void;
	onRemove: () => void;
	children?: ReactNode;
}

export function SortableFieldRow( {
	field,
	typeLabel,
	selected,
	onSelect,
	onDuplicate,
	onRemove,
	children,
}: Props ) {
	const {
		attributes,
		listeners,
		setNodeRef,
		transform,
		transition,
		isDragging,
	} = useSortable( { id: field.key } );

	const style: CSSProperties = {
		transform: CSS.Transform.toString( transform ),
		transition,
		opacity: isDragging ? 0.5 : 1,
	};

	const label = field.label || __( '(no label)', 'openfields' );

	return (
		<li
			ref={ setNodeRef }
			style={ style }
			className="openfields-field-row"
			aria-current={ selected ? 'true' : undefined }
		>
			<div className="openfields-field-row__header">
				<Button
					className="openfields-field-row__handle"
					icon="move"
					label={ sprintf(
						/* translators: %s: field label. */
						__( 'Reorder %s', 'openfields' ),
						label
					) }
					{ ...attributes }
					{ ...listeners }
				/>
				<Button
					className="openfields-field-row__title"
					variant="link"
					onClick={ onSelect }
					aria-expanded={ selected }
				>
					<span className="openfields-field-row__label">
						{ label }
					</span>
					<span className="openfields-field-row__type">
						{ typeLabel }
					</span>
				</Button>
				<Button
					size="small"
					variant="tertiary"
					onClick={ onDuplicate }
					label={ __( 'Duplicate field', 'openfields' ) }
					showTooltip
					icon="admin-page"
				/>
				<Button
					size="small"
					variant="tertiary"
					isDestructive
					onClick={ onRemove }
					label={ __( 'Delete field', 'openfields' ) }
					showTooltip
					icon="trash"
				/>
			</div>
			{ selected && (
				<div className="openfields-field-row__body">{ children }</div>
			) }
		</li>
	);
}
