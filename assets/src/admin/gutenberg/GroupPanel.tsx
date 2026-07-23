/**
 * A Gutenberg document settings panel rendering one field group, bound to post
 * meta via @wordpress/core-data.
 */

import {
	PluginDocumentSettingPanel,
	store as editorStore,
} from '@wordpress/editor';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { getFieldType } from '../../field-types/registry';
import type { FieldConfig } from '../../field-types/types';
import { FieldRenderer } from '../record/FieldRenderer';

export interface SidebarGroup {
	key: string;
	title: string;
	fields: FieldConfig[];
}

type Meta = Record< string, unknown >;

export function GroupPanel( { group }: { group: SidebarGroup } ) {
	const postType = useSelect(
		( select ) =>
			(
				select( editorStore ) as {
					getCurrentPostType: () => string | undefined;
				}
			 ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp(
		'postType',
		postType ?? '',
		'meta'
	) as unknown as [ Meta | undefined, ( next: Meta ) => void ];

	const [ touched, setTouched ] = useState< Record< string, boolean > >( {} );

	const values: Meta = meta ?? {};

	const onChange = ( name: string, value: unknown ) => {
		setMeta( { ...values, [ name ]: value } );
		setTouched( ( previous ) => ( { ...previous, [ name ]: true } ) );
	};

	const errorFor = ( field: FieldConfig ): string | undefined => {
		if ( ! touched[ field.name ] ) {
			return undefined;
		}
		const definition = getFieldType( field.type );
		return (
			definition?.validate?.( values[ field.name ] as never, field ) ??
			undefined
		);
	};

	return (
		<PluginDocumentSettingPanel
			name={ `openfields-${ group.key }` }
			title={ group.title || __( 'Fields', 'openfields' ) }
		>
			<FieldRenderer
				fields={ group.fields }
				values={ values }
				onChange={ onChange }
				errorFor={ errorFor }
			/>
		</PluginDocumentSettingPanel>
	);
}
