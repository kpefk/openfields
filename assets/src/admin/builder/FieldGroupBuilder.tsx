/**
 * Field Group Builder — root component.
 */

import { useReducer, useEffect, useState } from '@wordpress/element';
import {
	Panel,
	PanelBody,
	TextControl,
	SelectControl,
	Button,
	Flex,
	FlexItem,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	reducer,
	createInitialState,
	createField,
	type FieldGroupState,
	type FieldGroupInput,
} from './state';
import { FieldList } from './FieldList';
import { LocationRulesEditor } from './LocationRulesEditor';
import { GroupSettingsPanel } from './GroupSettings';
import { getFieldTypes } from '../../field-types/registry';
import { uniqueKey } from './util';

interface Props {
	initialState?: FieldGroupInput;
	onChange?: ( state: FieldGroupState ) => void;
}

export function FieldGroupBuilder( { initialState, onChange }: Props ) {
	const [ state, dispatch ] = useReducer(
		reducer,
		createInitialState( initialState )
	);
	const [ typeToAdd, setTypeToAdd ] = useState( 'text' );

	useEffect( () => {
		onChange?.( state );
	}, [ state, onChange ] );

	const typeOptions = getFieldTypes().map( ( definition ) => ( {
		value: definition.type,
		label: definition.label,
	} ) );

	const addField = () =>
		dispatch( {
			type: 'ADD_FIELD',
			field: createField( typeToAdd, uniqueKey() ),
		} );

	return (
		<div className="openfields-builder">
			<TextControl
				label={ __( 'Field group title', 'openfields' ) }
				value={ state.title }
				onChange={ ( title ) =>
					dispatch( { type: 'SET_TITLE', title } )
				}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			<Panel>
				<PanelBody title={ __( 'Fields', 'openfields' ) } initialOpen>
					<FieldList state={ state } dispatch={ dispatch } />

					<Flex
						className="openfields-builder__add-field"
						justify="flex-start"
						align="flex-end"
					>
						<FlexItem>
							<SelectControl
								label={ __( 'Field type', 'openfields' ) }
								value={ typeToAdd }
								options={
									typeOptions.length > 0
										? typeOptions
										: [
												{
													value: 'text',
													label: __(
														'Text',
														'openfields'
													),
												},
										  ]
								}
								onChange={ setTypeToAdd }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</FlexItem>
						<FlexItem>
							<Button variant="primary" onClick={ addField }>
								{ __( 'Add field', 'openfields' ) }
							</Button>
						</FlexItem>
					</Flex>
				</PanelBody>

				<PanelBody
					title={ __( 'Location Rules', 'openfields' ) }
					initialOpen={ false }
				>
					<LocationRulesEditor
						location={ state.location }
						dispatch={ dispatch }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Settings', 'openfields' ) }
					initialOpen={ false }
				>
					<GroupSettingsPanel
						settings={ state.settings }
						dispatch={ dispatch }
					/>
				</PanelBody>
			</Panel>
		</div>
	);
}
