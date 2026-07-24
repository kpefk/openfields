# Built-in field types

The 13 core field types. Use the **identifier** as a field's `type`. "Stored"
describes what `get_field( …, false )` returns (the raw value); the formatted
value may differ (e.g. an attachment is returned as an ID for now).

Type-specific keys live under the field's `settings`; `label`, `instructions`,
`required`, `placeholder` and `defaultValue` are top-level (see
[public-api.md](public-api.md)).

| Identifier | Label | Category | Stored value | `settings` keys |
|------------|-------|----------|--------------|-----------------|
| `text` | Text | basic | string | `maxlength` |
| `textarea` | Textarea | basic | string | — |
| `number` | Number | basic | int/float | `min`, `max` |
| `email` | Email | basic | string (validated) | — |
| `url` | URL | basic | string (validated) | — |
| `image` | Image | content | attachment ID | — |
| `file` | File | content | attachment ID | — |
| `wysiwyg` | WYSIWYG Editor | content | HTML string | — |
| `select` | Select | choice | string or string[] | `choices`, `multiple` |
| `checkbox` | Checkbox | choice | string[] | `choices` |
| `radio` | Radio Button | choice | string | `choices` |
| `true_false` | True / False | choice | `1`/`0` (boolean) | — |
| `message` | Message | layout | — (no value) | `message` |

## Choices

Choice fields (`select`, `checkbox`, `radio`) read their options from
`settings.choices`, a value → label map:

```php
array(
	'key'      => 'field_size',
	'name'     => 'size',
	'label'    => 'Size',
	'type'     => 'select',
	'settings' => array(
		'choices' => array(
			'sm' => 'Small',
			'md' => 'Medium',
			'lg' => 'Large',
		),
	),
),
```

## Validation

Each type validates both client-side (before submit) and server-side (on save
and via the API). The shared `required` rule is enforced for every value field;
`number` also checks `min`/`max`, `email`/`url` check their formats, and choice
fields check membership.

## UI-only types

`message` stores no value — it renders informational text (`settings.message`)
in the editor. It is skipped by storage, validation and the REST output.

## Custom types

Register your own types via the [Field Type API](field-types-api.md).
