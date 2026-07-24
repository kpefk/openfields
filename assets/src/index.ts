/**
 * OpenFields admin entry point.
 *
 * Registers the built-in field types and mounts the Field Group Builder on the
 * field-group edit screen (when its container is present).
 */

import './style.css';
import { registerCoreFieldTypes } from './field-types';
import { mountFieldGroupBuilder, mountRecordForms } from './admin';
import { registerGutenbergSidebar } from './admin/gutenberg';

registerCoreFieldTypes();
mountFieldGroupBuilder();
mountRecordForms();
registerGutenbergSidebar();

export const OPENFIELDS_VERSION = '0.1.0-alpha';
