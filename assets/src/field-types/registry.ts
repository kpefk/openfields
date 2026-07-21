/**
 * Field Type API — client-side registry.
 *
 * Mirrors the PHP `FieldTypeRegistry`: field types register a definition keyed
 * by their type identifier, and the builder / record form look them up here.
 */

import type { FieldTypeDefinition } from './types';

const registry = new Map< string, FieldTypeDefinition >();

/**
 * Register a field type, replacing any existing type with the same identifier.
 */
export function registerFieldType< TValue >(
	definition: FieldTypeDefinition< TValue >
): void {
	registry.set(
		definition.type,
		definition as unknown as FieldTypeDefinition
	);
}

/**
 * Retrieve a registered field type, or undefined when unknown.
 */
export function getFieldType(
	type: string
): FieldTypeDefinition | undefined {
	return registry.get( type );
}

/**
 * Whether a field type is registered.
 */
export function hasFieldType( type: string ): boolean {
	return registry.has( type );
}

/**
 * All registered field types, in registration order.
 */
export function getFieldTypes(): FieldTypeDefinition[] {
	return Array.from( registry.values() );
}

/**
 * Remove every registered field type. Intended for tests.
 */
export function clearFieldTypes(): void {
	registry.clear();
}
