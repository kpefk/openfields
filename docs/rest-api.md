# REST API

## Dedicated endpoint

```
GET /wp-json/openfields/v1/{post_type}/{id}
```

Returns all field values for a post.

| Query param | Values | Default | Description |
|-------------|--------|---------|-------------|
| `format` | `raw`, `formatted` | `formatted` | `raw` returns stored values; `formatted` runs them through their field types. |

**Example**

```
GET /wp-json/openfields/v1/post/42?format=formatted
```

```json
{
  "id": 42,
  "post_type": "post",
  "fields": {
    "headline": "Hello world",
    "count": 3
  }
}
```

**Permissions.** Published posts are readable by anyone; non-published posts
require the `read_post` capability for that post.

## Standard endpoints

Field values are also exposed on the standard `/wp/v2/*` post endpoints under the
`openfields_fields` key (formatted), for every post type with REST support:

```
GET /wp-json/wp/v2/posts/42
```

```json
{
  "id": 42,
  "openfields_fields": { "headline": "Hello world", "count": 3 }
}
```

## Versioning

The `openfields/v1` namespace is frozen: any breaking change ships under a new
namespace (`v2`), and `v1` is supported for at least one major version after
`v2` is released. Deprecations are announced in `CHANGELOG.md` ahead of time.

Writing values over REST is planned for a later phase.
