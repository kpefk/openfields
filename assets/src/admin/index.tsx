/**
 * Admin entry — mounts the Field Group Builder on the field-group edit screen.
 */

import { createRoot } from '@wordpress/element';
import { FieldGroupBuilder } from './builder/FieldGroupBuilder';
import { RecordForm } from './record/RecordForm';
import {
	createInitialState,
	serialize,
	type FieldGroupInput,
	type GroupSettings,
	type LocationGroup,
} from './builder/state';
import type { FieldConfig } from '../field-types/types';

function parseJson< T >( raw: string | undefined, fallback: T ): T {
	if ( ! raw ) {
		return fallback;
	}
	try {
		return JSON.parse( raw ) as T;
	} catch {
		return fallback;
	}
}

interface PersistedConfig {
	key?: string;
	title?: string;
	fields?: FieldConfig[];
	location?: LocationGroup[];
	settings?: Partial< GroupSettings >;
}

function readInitialState( container: HTMLElement ): FieldGroupInput {
	const raw = container.dataset.config;
	if ( ! raw ) {
		return {};
	}

	try {
		const config = JSON.parse( raw ) as PersistedConfig;
		return {
			key: config.key,
			title: config.title,
			fields: config.fields,
			location: config.location,
			settings: config.settings,
		};
	} catch {
		return {};
	}
}

/**
 * Mount the builder if its container is present on the page.
 */
export function mountFieldGroupBuilder(): void {
	const container = document.getElementById(
		'openfields-field-group-builder'
	);
	if ( ! container ) {
		return;
	}

	const input = document.getElementById(
		'openfields-field-group-data'
	) as HTMLInputElement | null;

	const initialState = readInitialState( container );

	// Seed the hidden input so a save without edits still persists the config.
	if ( input ) {
		input.value = JSON.stringify(
			serialize( createInitialState( initialState ) )
		);
	}

	const root = createRoot( container );
	root.render(
		<FieldGroupBuilder
			initialState={ initialState }
			onChange={ ( state ) => {
				if ( input ) {
					input.value = JSON.stringify( serialize( state ) );
				}
			} }
		/>
	);
}

/**
 * Mount the record form into every field-group meta box on the current screen.
 */
export function mountRecordForms(): void {
	const containers = document.querySelectorAll< HTMLElement >(
		'.openfields-record-form'
	);

	containers.forEach( ( container ) => {
		const inputId = container.dataset.inputId;
		const input = inputId
			? ( document.getElementById( inputId ) as HTMLInputElement | null )
			: null;

		const group = parseJson< { fields?: FieldConfig[] } >(
			container.dataset.group,
			{}
		);
		const values = parseJson< Record< string, unknown > >(
			container.dataset.values,
			{}
		);
		const serverErrors = parseJson< Record< string, string > >(
			container.dataset.errors,
			{}
		);
		const fields = Array.isArray( group.fields ) ? group.fields : [];

		// Seed the hidden input so a save without edits keeps existing values.
		if ( input ) {
			input.value = JSON.stringify( values );
		}

		const root = createRoot( container );
		root.render(
			<RecordForm
				fields={ fields }
				initialValues={ values }
				serverErrors={ serverErrors }
				onChange={ ( next ) => {
					if ( input ) {
						input.value = JSON.stringify( next );
					}
				} }
			/>
		);
	} );
}
