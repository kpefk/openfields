/**
 * Admin entry — mounts the Field Group Builder on the field-group edit screen.
 */

import { createRoot } from '@wordpress/element';
import { FieldGroupBuilder } from './builder/FieldGroupBuilder';
import {
	createInitialState,
	serialize,
	type FieldGroupInput,
	type GroupSettings,
	type LocationGroup,
} from './builder/state';
import type { FieldConfig } from '../field-types/types';

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
