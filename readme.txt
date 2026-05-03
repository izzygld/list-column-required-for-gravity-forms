=== List Column Required for Gravity Forms ===
Contributors: izzygld
Donate link: https://github.com/izzygld
Tags: gravity forms, list field, required columns, validation, form editor
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Per-column required validation for Gravity Forms List fields — mark individual columns as required right in the form editor.

== Description ==

**List Column Required for Gravity Forms** lets you mark individual columns as **required** in multi-column List fields — something Gravity Forms doesn't support out of the box.

By default, Gravity Forms only allows you to make the entire List field required (meaning at least one row must be filled). This plugin goes further: you can choose **which columns** within each row must be filled in before the form can be submitted.

= The Problem =

When using the Gravity Forms **List** field with "Enable multiple columns" turned on, there's no way to require specific columns. You can only require the entire field, which passes as long as *any* cell has a value — even if critical columns like "Email" or "Name" are left blank.

= The Solution =

This plugin adds a small **"Required"** checkbox next to each column in the form editor. Check it, and that column becomes required — with server-side validation, front-end asterisk indicators, and accessible `aria-required` attributes.

= Key Features =

* **Per-Column Required** — Mark individual columns as required independently
* **Server-Side Validation** — Required columns are enforced on form submission
* **Front-End Indicators** — Red asterisks on required column headers, matching GF native styling
* **Accessibility** — Adds `aria-required="true"` to required column inputs
* **Auto-Require All** — Toggling the field-level "Required" rule ON automatically marks all columns required
* **Smart Row Handling** — Blank extra rows are skipped; partially filled rows enforce required columns
* **Zero Configuration** — No settings pages; works at the field level out of the box

= How It Works =

1. Edit any form with a multi-column List field
2. Check the "Required" checkbox next to individual columns in the form editor
3. Save the form — required columns are enforced on submission

= Validation Logic =

* **Row with some data** → all required columns in that row must be filled
* **All rows empty** → the first row's required columns are enforced
* **Extra blank rows** → skipped (user added a row but didn't start filling it)
* **Non-column List fields** → completely unaffected

== Installation ==

1. Upload the `list-column-required-for-gravity-forms` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit any form with a List field → enable multiple columns → check "Required" on the columns you want

No configuration pages or global settings needed.

== Frequently Asked Questions ==

= Does this require Gravity Forms? =

Yes. Gravity Forms 2.5 or higher must be installed and activated.

= Does it work with single-column List fields? =

No — single-column List fields don't have individual columns to mark as required. Use the standard field-level "Required" setting for those.

= Where is the "Required" checkbox? =

In the form editor, click on a List field with multiple columns enabled. Each column in the "Columns" section will have a "Required" checkbox next to it.

= Does it create any database tables? =

No. Column required state is stored in the existing form JSON as an `isColumnRequired` flag on each column choice object.

== Screenshots ==

1. Form editor showing the per-column "Required" checkbox
2. Front-end form with required column asterisk indicators

== Changelog ==

= 1.2.0 =
* Cell-level error highlighting: only the specific empty required cells in a List field now show the red error border, instead of every cell in the field.
* Accessibility: `aria-invalid="true"` is now set only on the failing cells (was previously a blanket effect).
* New CSS hooks for theming: required cells get `lcr-gf-required-cell` and `data-lcr-col="Column Name"`; failing cells additionally get `lcr-gf-cell-error`.
* Subtle visual marker on required-but-currently-valid cells (inset border on the left edge) inside the GF framework theme.

= 1.1.2 =
* Maintenance release: added automated WordPress.org SVN deployment workflow and official plugin assets (icon, banner, screenshots). No functional changes from 1.1.0.

= 1.1.1 =
* Skipped (release-tooling test).

= 1.1.0 =
* Accessibility + consistency: when any column is marked required (but the field-level "Required" is off), the field label now shows the standard asterisk and the form displays the "* indicates required fields" legend at the top — matching how GF natively signals required fields. Validation behavior is unchanged.

= 1.0.0 =
* Initial release
* Per-column required checkboxes in form editor
* Server-side validation for required columns
* Front-end asterisk indicators and aria-required attributes
* Smart row handling (skip blank extra rows)
* Auto-require all columns when field-level required is toggled ON

== Upgrade Notice ==

= 1.2.0 =
Error highlighting is now per-cell instead of per-field — only the actual empty required cells get the red border. No action required, no breaking changes.

= 1.1.2 =
Maintenance release. Safe to upgrade — no functional changes.

= 1.1.0 =
Adds field-label asterisk + "indicates required fields" legend when any column is required, for better accessibility and consistency with native GF required fields.

= 1.0.0 =
Initial release.
