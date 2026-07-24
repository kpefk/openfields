=== OpenFields ===
Contributors: kpefk
Tags: custom fields, fields, acf, field groups, gutenberg
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.1.0-alpha
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Open Source custom fields and field groups for WordPress — a GPL-compatible alternative to ACF, with Gutenberg and REST support.

== Description ==

OpenFields lets you attach custom fields to your content through a familiar
field-group builder, and read them back with a simple template API. It is fully
GPL and free to fork, redistribute and bundle.

**This is a pre-release (0.x) under active development.** APIs and data formats
may change until 1.0.0.

= What you get =

* A drag-and-drop **field group builder** with per-field settings.
* **13 field types**: Text, Textarea, Number, Email, URL, Image, File, WYSIWYG,
  Select, Checkbox, Radio, True/False and Message.
* **Location rules** to attach groups to post types, templates, taxonomies,
  user roles and more.
* **Conditional logic** that shows and hides fields live as their dependencies
  change.
* **Gutenberg** support — fields appear in a document settings panel — and
  classic-editor meta boxes.
* A **template API** (`get_field()`, `get_fields()`, `update_field()`) and code
  registration (`openfields_add_local_field_group()`).
* **REST API** read access, plus values on the standard `/wp/v2/*` endpoints.
* **Import/Export** (JSON and generated PHP) and **Local JSON** sync between
  environments.
* Fully translatable, RTL-ready admin.

= Roadmap =

Repeater and Flexible Content fields, Clone, Gallery, Options Pages, OpenFields
Blocks, a GraphQL connector and REST write access are planned for later releases.

= Developer documentation =

See the project repository for the full documentation, including the public API,
field-type API, hooks and REST reference: https://github.com/kpefk/openfields

== Installation ==

1. Upload the plugin to `/wp-content/plugins/openfields`, or install it from the
   Plugins screen.
2. Activate **OpenFields** through the *Plugins* screen.
3. Create your first group under **Field Groups → Add New**.

OpenFields cannot run alongside Advanced Custom Fields — they define the same
functions. Deactivate one of them.

== Frequently Asked Questions ==

= Does it work with the block editor (Gutenberg)? =

Yes. Matching field groups render in a document settings panel, and values are
saved through the standard post-meta REST flow.

= Can I run it together with ACF? =

No. Both provide functions like `get_field()`, so OpenFields deactivates itself
(with a notice) when ACF is active.

= How do I register fields in code? =

Use `openfields_add_local_field_group()`, or drop a JSON export into an
`openfields-json/` directory in your theme.

= Where are values stored? =

In standard post meta, keyed by the field name — no custom database tables.

== Changelog ==

= 0.1.0-alpha =
* Field group builder with 13 field types, location rules and conditional logic.
* Classic-editor meta boxes and a Gutenberg document settings panel.
* Public PHP API, REST read endpoints, Import/Export and Local JSON.
