/**
 * Field Type API — public entry point.
 *
 * Re-exports the contract and registry, and registers the built-in field types.
 */

export * from './types';
export * from './registry';

import { registerFieldType } from './registry';
import { textField } from './fields/Text';
import { textareaField } from './fields/Textarea';
import { numberField } from './fields/Number';
import { emailField } from './fields/Email';
import { urlField } from './fields/Url';
import { imageField } from './fields/Image';
import { fileField } from './fields/File';
import { wysiwygField } from './fields/Wysiwyg';
import { selectField } from './fields/Select';
import { checkboxField } from './fields/Checkbox';
import { radioField } from './fields/Radio';
import { trueFalseField } from './fields/TrueFalse';
import { messageField } from './fields/Message';

/**
 * Register the built-in (core) field types.
 */
export function registerCoreFieldTypes(): void {
	registerFieldType( textField );
	registerFieldType( textareaField );
	registerFieldType( numberField );
	registerFieldType( emailField );
	registerFieldType( urlField );
	registerFieldType( imageField );
	registerFieldType( fileField );
	registerFieldType( wysiwygField );
	registerFieldType( selectField );
	registerFieldType( checkboxField );
	registerFieldType( radioField );
	registerFieldType( trueFalseField );
	registerFieldType( messageField );
}
