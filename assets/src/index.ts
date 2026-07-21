/**
 * OpenFields admin entry point.
 *
 * Registers the built-in field types on load. The group builder and record form
 * (added in later milestones) consume the registry populated here.
 */

import { registerCoreFieldTypes } from './field-types';

registerCoreFieldTypes();

export const OPENFIELDS_VERSION = '0.1.0-alpha';
