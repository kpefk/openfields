/**
 * Message field type (client) — informational, stores no value.
 */

import { __ } from '@wordpress/i18n';
import type { FieldEditProps, FieldTypeDefinition } from '../types';

function MessageEdit( { config }: FieldEditProps< null > ) {
	const message =
		typeof config.settings?.message === 'string'
			? config.settings.message
			: config.label;

	return <p className="openfields-message">{ message }</p>;
}

export const messageField: FieldTypeDefinition< null > = {
	type: 'message',
	label: __( 'Message', 'openfields' ),
	category: 'layout',
	EditComponent: MessageEdit,
	getDefaultConfig: () => ( {} ),
};
