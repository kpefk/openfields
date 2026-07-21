/**
 * Field Group Builder — group-level settings.
 */

import {
	SelectControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { Dispatch } from 'react';
import type { BuilderAction, GroupSettings } from './state';

interface Props {
	settings: GroupSettings;
	dispatch: Dispatch< BuilderAction >;
}

export function GroupSettingsPanel( { settings, dispatch }: Props ) {
	const update = ( changes: Partial< GroupSettings > ) =>
		dispatch( { type: 'UPDATE_SETTINGS', changes } );

	return (
		<div className="openfields-group-settings">
			<SelectControl
				label={ __( 'Position', 'openfields' ) }
				value={ settings.position }
				options={ [
					{
						value: 'normal',
						label: __( 'Normal (after content)', 'openfields' ),
					},
					{ value: 'side', label: __( 'Side', 'openfields' ) },
					{
						value: 'acf_after_title',
						label: __( 'High (after title)', 'openfields' ),
					},
				] }
				onChange={ ( value ) =>
					update( { position: value as GroupSettings[ 'position' ] } )
				}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<SelectControl
				label={ __( 'Style', 'openfields' ) }
				value={ settings.style }
				options={ [
					{
						value: 'default',
						label: __( 'Standard (metabox)', 'openfields' ),
					},
					{
						value: 'seamless',
						label: __( 'Seamless (no box)', 'openfields' ),
					},
				] }
				onChange={ ( value ) =>
					update( { style: value as GroupSettings[ 'style' ] } )
				}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<SelectControl
				label={ __( 'Label placement', 'openfields' ) }
				value={ settings.label_placement }
				options={ [
					{ value: 'top', label: __( 'Top', 'openfields' ) },
					{ value: 'left', label: __( 'Left', 'openfields' ) },
				] }
				onChange={ ( value ) =>
					update( {
						label_placement:
							value as GroupSettings[ 'label_placement' ],
					} )
				}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<TextControl
				type="number"
				label={ __( 'Order', 'openfields' ) }
				value={ String( settings.menu_order ) }
				onChange={ ( value ) =>
					update( { menu_order: Number( value || 0 ) } )
				}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<TextareaControl
				label={ __( 'Description', 'openfields' ) }
				value={ settings.description }
				onChange={ ( description ) => update( { description } ) }
				__nextHasNoMarginBottom
			/>
		</div>
	);
}
