/**
 * OpenFields admin entry point.
 *
 * Registers the built-in field types and mounts the Field Group Builder on the
 * field-group edit screen (when its container is present).
 */

import { registerCoreFieldTypes } from './field-types';
import { mountFieldGroupBuilder } from './admin';

registerCoreFieldTypes();
mountFieldGroupBuilder();

export const OPENFIELDS_VERSION = '0.1.0-alpha';
