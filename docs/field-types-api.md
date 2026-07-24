# Field Type API

A field type has two halves that share the same `type` identifier: a PHP class
(sanitize, validate, format, storage schema) and a TypeScript definition (the
edit component and client-side validation).

## PHP

Extend `OpenFields\FieldTypes\AbstractFieldType`:

```php
use OpenFields\FieldTypes\AbstractFieldType;

final class RatingFieldType extends AbstractFieldType {

	public function get_type(): string {
		return 'acme_rating';
	}

	public function get_label(): string {
		return __( 'Rating', 'acme' );
	}

	public function get_category(): string {
		return 'choice';
	}

	public function get_rest_type(): string {
		return 'integer';
	}

	public function sanitize( $value, array $field = array() ) {
		return max( 0, min( 5, absint( $value ) ) );
	}

	protected function validate_value( $value, array $field ) {
		if ( (int) $value < 0 || (int) $value > 5 ) {
			return new \WP_Error( 'acme_rating', __( 'Rating must be 0–5.', 'acme' ) );
		}
		return true;
	}
}
```

Register it (e.g. on the `openfields/register_field_types` action, or anytime):

```php
openfields_register_field_type( \Acme\RatingFieldType::class );
```

`AbstractFieldType` provides the shared `required` check in `validate()`; override
`validate_value()` for type-specific rules (it runs only for non-empty values).

## TypeScript

Register a definition implementing `FieldTypeDefinition` from the
`assets/src/field-types/types.ts` contract:

```tsx
import { registerFieldType } from '@fields/registry';
import type { FieldTypeDefinition } from '@fields/types';

const ratingField: FieldTypeDefinition< number > = {
	type: 'acme_rating',
	label: 'Rating',
	category: 'choice',
	EditComponent: ( { config, value, onChange } ) => (
		<StarRating label={ config.label } value={ value } onChange={ onChange } />
	),
	getDefaultConfig: () => ( { defaultValue: 0 } ),
	validate: ( value, config ) =>
		config.required && ! value ? 'This field is required.' : null,
};

registerFieldType( ratingField );
```

The `type` identifiers must match between the two halves.
