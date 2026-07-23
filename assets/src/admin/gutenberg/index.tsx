/**
 * Registers the OpenFields Gutenberg sidebar plugin, rendering a document
 * settings panel for each matching field group (localised as
 * `window.openfieldsEditor.groups` by the server).
 */

import { registerPlugin } from '@wordpress/plugins';
import { GroupPanel, type SidebarGroup } from './GroupPanel';

interface EditorData {
	groups: SidebarGroup[];
}

declare global {
	interface Window {
		openfieldsEditor?: EditorData;
	}
}

/**
 * Register the sidebar plugin if the editor data is present.
 */
export function registerGutenbergSidebar(): void {
	const data = window.openfieldsEditor;

	if (
		! data ||
		! Array.isArray( data.groups ) ||
		data.groups.length === 0
	) {
		return;
	}

	const groups = data.groups;

	registerPlugin( 'openfields', {
		render: () => (
			<>
				{ groups.map( ( group ) => (
					<GroupPanel key={ group.key } group={ group } />
				) ) }
			</>
		),
	} );
}
