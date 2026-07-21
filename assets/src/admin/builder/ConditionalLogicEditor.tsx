/**
 * Field Group Builder — conditional logic editor for a field.
 */

import {
	Button,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type {
	ConditionalLogicGroup,
	ConditionalLogicRule,
	FieldConfig,
} from '../../field-types/types';

interface Props {
	field: FieldConfig;
	allFields: FieldConfig[];
	onChange: ( logic: ConditionalLogicGroup[] | false ) => void;
}

type Operator = ConditionalLogicRule[ 'operator' ];

const OPERATORS: ReadonlyArray< { value: Operator; label: string } > = [
	{ value: '==', label: __( 'is equal to', 'openfields' ) },
	{ value: '!=', label: __( 'is not equal to', 'openfields' ) },
	{ value: 'contains', label: __( 'contains', 'openfields' ) },
	{ value: 'empty', label: __( 'is empty', 'openfields' ) },
	{ value: 'not_empty', label: __( 'is not empty', 'openfields' ) },
	{ value: 'matches', label: __( 'matches pattern', 'openfields' ) },
];

function emptyRule( fieldKey: string ): ConditionalLogicRule {
	return { field: fieldKey, operator: '==', value: '' };
}

export function ConditionalLogicEditor( {
	field,
	allFields,
	onChange,
}: Props ) {
	const enabled = Array.isArray( field.conditionalLogic );
	const groups: ConditionalLogicGroup[] = enabled
		? ( field.conditionalLogic as ConditionalLogicGroup[] )
		: [];

	const candidates = allFields.filter( ( other ) => other.key !== field.key );

	const fieldOptions = candidates.map( ( other ) => ( {
		value: other.key,
		label: other.label || other.name || other.key,
	} ) );

	const toggle = ( on: boolean ) => {
		if ( ! on ) {
			onChange( false );
			return;
		}
		const first = candidates[ 0 ]?.key ?? '';
		onChange( [ [ emptyRule( first ) ] ] );
	};

	const updateRule = (
		groupIndex: number,
		ruleIndex: number,
		changes: Partial< ConditionalLogicRule >
	) => {
		onChange(
			groups.map( ( group, gi ) =>
				gi === groupIndex
					? group.map( ( rule, ri ) =>
							ri === ruleIndex ? { ...rule, ...changes } : rule
					  )
					: group
			)
		);
	};

	const removeRule = ( groupIndex: number, ruleIndex: number ) => {
		const next = groups
			.map( ( group, gi ) =>
				gi === groupIndex
					? group.filter( ( _, ri ) => ri !== ruleIndex )
					: group
			)
			.filter( ( group ) => group.length > 0 );
		onChange( next.length > 0 ? next : false );
	};

	const addRule = ( groupIndex: number ) => {
		const first = candidates[ 0 ]?.key ?? '';
		onChange(
			groups.map( ( group, gi ) =>
				gi === groupIndex ? [ ...group, emptyRule( first ) ] : group
			)
		);
	};

	const addGroup = () => {
		const first = candidates[ 0 ]?.key ?? '';
		onChange( [ ...groups, [ emptyRule( first ) ] ] );
	};

	return (
		<div className="openfields-conditional-logic">
			<ToggleControl
				label={ __( 'Conditional logic', 'openfields' ) }
				checked={ enabled }
				onChange={ toggle }
				disabled={ candidates.length === 0 }
				help={
					candidates.length === 0
						? __(
								'Add another field to enable conditional logic.',
								'openfields'
						  )
						: undefined
				}
				__nextHasNoMarginBottom
			/>

			{ enabled &&
				groups.map( ( group, groupIndex ) => (
					// eslint-disable-next-line react/no-array-index-key
					<fieldset
						key={ groupIndex }
						className="openfields-cl-group"
					>
						<legend>
							{ groupIndex === 0
								? __( 'Show this field if', 'openfields' )
								: __( 'or if', 'openfields' ) }
						</legend>
						{ group.map( ( rule, ruleIndex ) => (
							// eslint-disable-next-line react/no-array-index-key
							<div
								key={ ruleIndex }
								className="openfields-cl-rule"
							>
								<SelectControl
									label={ __( 'Field', 'openfields' ) }
									hideLabelFromVision
									value={ rule.field }
									options={ fieldOptions }
									onChange={ ( value ) =>
										updateRule( groupIndex, ruleIndex, {
											field: value,
										} )
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<SelectControl
									label={ __( 'Operator', 'openfields' ) }
									hideLabelFromVision
									value={ rule.operator }
									options={ OPERATORS.map( ( op ) => ( {
										value: op.value,
										label: op.label,
									} ) ) }
									onChange={ ( value ) =>
										updateRule( groupIndex, ruleIndex, {
											operator: value as Operator,
										} )
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<TextControl
									label={ __( 'Value', 'openfields' ) }
									hideLabelFromVision
									value={
										typeof rule.value === 'string'
											? rule.value
											: ''
									}
									onChange={ ( value ) =>
										updateRule( groupIndex, ruleIndex, {
											value,
										} )
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<Button
									size="small"
									variant="tertiary"
									isDestructive
									icon="no-alt"
									label={ __( 'Remove rule', 'openfields' ) }
									onClick={ () =>
										removeRule( groupIndex, ruleIndex )
									}
								/>
							</div>
						) ) }
						<Button
							size="small"
							variant="secondary"
							onClick={ () => addRule( groupIndex ) }
						>
							{ __( 'And', 'openfields' ) }
						</Button>
					</fieldset>
				) ) }

			{ enabled && (
				<Button variant="secondary" onClick={ addGroup }>
					{ __( 'Add "or" group', 'openfields' ) }
				</Button>
			) }
		</div>
	);
}
