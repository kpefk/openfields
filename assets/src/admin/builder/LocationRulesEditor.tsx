/**
 * Field Group Builder — location rules editor.
 */

import { Button, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { Dispatch } from 'react';
import type { BuilderAction, LocationGroup, LocationRule } from './state';

interface Props {
	location: LocationGroup[];
	dispatch: Dispatch< BuilderAction >;
}

const PARAMS: ReadonlyArray< { value: string; label: string } > = [
	{ value: 'post_type', label: __( 'Post Type', 'openfields' ) },
	{ value: 'post_status', label: __( 'Post Status', 'openfields' ) },
	{ value: 'page_template', label: __( 'Page Template', 'openfields' ) },
	{ value: 'user_role', label: __( 'User Role', 'openfields' ) },
	{ value: 'taxonomy', label: __( 'Taxonomy', 'openfields' ) },
	{ value: 'options_page', label: __( 'Options Page', 'openfields' ) },
];

const OPERATORS: ReadonlyArray< {
	value: LocationRule[ 'operator' ];
	label: string;
} > = [
	{ value: '==', label: __( 'is equal to', 'openfields' ) },
	{ value: '!=', label: __( 'is not equal to', 'openfields' ) },
];

function emptyRule(): LocationRule {
	return { param: 'post_type', operator: '==', value: '' };
}

export function LocationRulesEditor( { location, dispatch }: Props ) {
	const set = ( next: LocationGroup[] ) =>
		dispatch( { type: 'SET_LOCATION', location: next } );

	const updateRule = (
		groupIndex: number,
		ruleIndex: number,
		changes: Partial< LocationRule >
	) => {
		set(
			location.map( ( group, gi ) =>
				gi === groupIndex
					? group.map( ( rule, ri ) =>
							ri === ruleIndex ? { ...rule, ...changes } : rule
					  )
					: group
			)
		);
	};

	const removeRule = ( groupIndex: number, ruleIndex: number ) => {
		set(
			location
				.map( ( group, gi ) =>
					gi === groupIndex
						? group.filter( ( _, ri ) => ri !== ruleIndex )
						: group
				)
				.filter( ( group ) => group.length > 0 )
		);
	};

	const addRule = ( groupIndex: number ) =>
		set(
			location.map( ( group, gi ) =>
				gi === groupIndex ? [ ...group, emptyRule() ] : group
			)
		);

	const addGroup = () => set( [ ...location, [ emptyRule() ] ] );

	return (
		<div className="openfields-location-rules">
			<p>
				{ __(
					'Show this field group when any of these rule groups match.',
					'openfields'
				) }
			</p>

			{ location.map( ( group, groupIndex ) => (
				// eslint-disable-next-line react/no-array-index-key
				<fieldset key={ groupIndex } className="openfields-loc-group">
					<legend>
						{ groupIndex === 0
							? __( 'Show if', 'openfields' )
							: __( 'or', 'openfields' ) }
					</legend>
					{ group.map( ( rule, ruleIndex ) => (
						// eslint-disable-next-line react/no-array-index-key
						<div key={ ruleIndex } className="openfields-loc-rule">
							<SelectControl
								label={ __( 'Parameter', 'openfields' ) }
								hideLabelFromVision
								value={ rule.param }
								options={ PARAMS.map( ( param ) => ( {
									value: param.value,
									label: param.label,
								} ) ) }
								onChange={ ( param ) =>
									updateRule( groupIndex, ruleIndex, {
										param,
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
										operator:
											value as LocationRule[ 'operator' ],
									} )
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextControl
								label={ __( 'Value', 'openfields' ) }
								hideLabelFromVision
								value={ rule.value }
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

			<Button variant="secondary" onClick={ addGroup }>
				{ __( 'Add rule group', 'openfields' ) }
			</Button>
		</div>
	);
}
