# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2.9.0] - 2026-05-15

### Added

- **Six new Custom Field types**: `date`, `time`, `email`, `number`, `color`, `range`.
  - `date` and `time` store ISO / 24-hour values and render via WordPress's site-wide `date_format` / `time_format` by default, with `output="raw"` for the as-stored string and `output="timestamp"` for Unix seconds (date only).
  - `email` is the first field type whose Elementor dynamic-tag binding is **multi-category** — it appears in both the TEXT dropdown (Heading / Button label) and the URL dropdown (Button URL, bound as `mailto:`). Shortcode supports `output="mailto"` and `output="link"` variants.
  - `number` uses `FILTER_VALIDATE_INT` so `'1.5'` is rejected rather than silently truncated. `color` validates `#RRGGBB` and lowercases on save. `range` is an **open-bounded integer pair** (Min/Max) — pick any integers (e.g. 50–200, 1–10, -40–40); stored as `"min,max"`. Render variants: `output="min"`, `output="max"`, `output="raw"`. Sanitize swaps Min and Max if entered out of order so the stored form's ordering invariant always holds.
- **Array-shaped `el_category` in the type registry** — `'text'` or `['text', 'url']`. Backward-compatible: existing string-valued types keep working. Filter `paradise_custom_field_types` lets sites add their own multi-category types without forking.
- **Live hex display next to the color picker** — admin shows the chosen `#RRGGBB` next to the native swatch and updates as the user hovers/picks.

## [2.8.1] - 2026-05-15

### Fixed

- **Cookie Consent Bar widget icon** — was `eicon-cookie`, which doesn't exist in Elementor's icon font, so the widget appeared icon-less in the editor's widget panel. Replaced with `eicon-info-circle` (visually distinct from the Announcement Bar's `eicon-alert`).

## [2.8.0] - 2026-05-15

### Added

- **Custom Fields** — user-defined static fields organized into groups, accessed via shortcode and Elementor Dynamic Tags. Field keys are globally unique; groups exist only for admin organization. First types shipped: text, textarea, url, image. Storage in `paradise_custom_fields` option; field rendering driven by a type registry (`sanitize` + `render` callbacks per type) so adding a new type is one entry. Filter `paradise_custom_field_types` lets sites add their own types without forking. Shortcode: `[paradise_field key="..." output="..."]` — `output` is per-type (e.g. `html` on image renders `<img>` with `srcset` via `wp_get_attachment_image()`).
- **Elementor Dynamic Tags for Custom Fields** — three tags under a "Paradise Custom Fields" group: `paradise-cf-text` (TEXT_CATEGORY, surfaces text + textarea fields), `paradise-cf-url` (URL_CATEGORY), and `paradise-cf-image` (IMAGE_CATEGORY, returns `{id, url}` so responsive `srcset` works). Each tag's field SELECT is built from the type registry's `el_category` mapping — adding new fields populates the dropdown automatically.
- **"Custom Fields" admin page** under the Paradise menu with drag-to-reorder for groups and fields, type-aware value inputs (one variant active at a time via CSS attribute selectors), and the WP media picker for image fields. Group slug and field key auto-derive from their label (`sanitize_title`) when empty so a casual save doesn't silently drop data.

## [2.7.0] - 2026-05-15

### Added

- **Feature Card example widget** (`widgets/class-paradise-feature-card-example.php`) — a heavily-commented reference widget for developers extending Paradise. Lives in its own "Paradise Examples" Elementor category, disabled by default so it doesn't appear on end-user sites. Introduces two optional registry flags: `example` (metadata for future UI grouping) and `default` (per-widget enabled-by-default state — defaults to `true` if absent, set `false` for example widgets). `Paradise_EW_Admin::get()` and `widget_enabled()` honour the per-widget `default`. Companion CSS at `assets/css/feature-card-example.css`
- **Per-row Copy Shortcode buttons** on Site Info phone, email, and social link rows. Each button reads the row's *current* (unsaved) label or platform value and writes the corresponding `[paradise_site_info ...]` shortcode to the clipboard. Click feedback: green checkmark icon swap plus a "Copied!" toast above the button (~1.5s slide-up + fade-out)
- **Brand-coloured platform icons** next to each social `<select>` on the Site Info admin page (Instagram, Facebook, X, LinkedIn, YouTube, TikTok, Pinterest, Snapchat, Threads, WhatsApp). Updates live when the platform selection changes. New helper `Paradise_Site_Info::social_icon_svg()` is the single source of truth for both initial PHP render and live JS update
- **Settings page improvements** — widgets grouped into three cards (Production widgets / Developer Examples / Features), each with Enable all / Disable all bulk actions. A live filter input above the form narrows the visible list as you type. "Off by default" badge surfaces example widgets that ship disabled
- **"Unsaved changes" pill** in the Settings and Site Info page headers. Appears on first edit; a native `beforeunload` warning blocks accidental navigation. Clears on save
- **Confirm dialog before destructive actions** on Site Info — wordier copy for locations (which carry phones, emails, address, and hours) and short copy for row-level deletes
- **Visual separation between Locations and their sub-sections** on Site Info — each location reads as a distinct card with phones / emails / address / hours as clearly separated panels inside

### Changed

- **Site Info admin page** — card layout reshaped (consistent padding, location cards as distinct units), Remove and Add buttons restyled as icon-only with leading plus glyphs and a soft-red destructive tint, intro rewritten as a two-method callout (Dynamic Tags recommended; shortcode for outside Elementor)
- **Import / Export admin page** — header now matches the Settings page (title + version badge); previously-inline `<style>` block moved to `assets/css/admin.css`
- **Admin asset enqueue** — `paradise-ew-admin` script (`admin.js`) now enqueued alongside the existing CSS on every Paradise admin page. Page detection uses a prefix check (`toplevel_page_` + `paradise_page_`) so new submenu pages get the assets automatically

### Fixed

- **Copy Shortcode works on plain HTTP** (e.g. local dev on Valet `*.test` domains). `navigator.clipboard` is only defined in secure contexts, so the original handler silently failed on HTTP. Now falls back to a temporary `<textarea>` + `document.execCommand('copy')` — universally supported, works in non-secure contexts. A red "Copy failed" toast surfaces if both paths fail, so silent failures are gone in both directions
- **Google Maps preview iframe no longer flickers with 400 errors** during typing or on malformed URLs. The map URL field's live preview now validates the URL as an `https://google.com/maps/embed` URL before pointing the iframe at it; invalid URLs hide the preview and skip the load
- **Save confirmation visible after long-form save** — Site Info redirects back to a long page after submit; some browsers preserve scroll position and the user lands near the Save button, missing the success notice at the top of `.wrap`. The page now smooth-scrolls the success notice into view on the post-save reload

### Documentation

- **README** — Developer Guide gains a "Learning from the example widget" subsection that points new contributors at `widgets/class-paradise-feature-card-example.php` as a complete, commented blueprint covering registry registration, Elementor controls, render output, asset loading, and the conventions enforced by `Paradise_Widget_Base`

---

## [2.6.0] - 2026-05-14

### Added

- **`Paradise_Widget_Base` abstract class** (`includes/class-paradise-widget-base.php`) — a small shared base inherited by every bundled widget. Centralises the behaviour that was identical across the widget set: `get_categories()` returns `[ 'paradise' ]`, `get_style_depends()` returns the conventional `paradise-{slug}` handle derived from `get_name()`, and `get_script_depends()` defaults to `[]`. Exposes a `get_default_handle()` helper so subclasses with a JS file can write `return [ $this->get_default_handle() ]` instead of repeating a hardcoded handle string. Plugins or themes that fork Paradise can extend the same base to inherit these conventions

### Changed

- **All 15 bundled widgets migrated to `Paradise_Widget_Base`** — each widget drops its hand-written `get_categories()` and `get_style_depends()` overrides where they matched the base default. Widgets that ship a JS file declare `get_script_depends()` through `$this->get_default_handle()` instead of a hardcoded string. Bottom Navigation Bar preserves its extra Font Awesome handles via `array_merge( parent::get_style_depends(), [ … ] )`. No behaviour change for end users — same categories, same asset handles, same render output
- **README** — File Structure now lists `includes/class-paradise-widget-base.php` and notes that every widget file under `widgets/` extends it. The Developer Guide's "Adding a new widget" section is rewritten to reflect the current workflow: extend `Paradise_Widget_Base`, add a registry entry with just `label`/`description`/optional `js`, drop asset files at the conventional paths, and let the loader handle the rest. Includes the `array_merge( parent::get_style_depends(), … )` pattern for widgets that need extra handles

---

## [2.5.0] - 2026-05-13

### Changed

- **Registry-driven asset registration** — `enqueue_assets()` reduced from ~170 lines of hand-written `wp_register_style` / `wp_register_script` calls to a single loop over `Paradise_EW_Admin::$widget_registry`. Adding a new widget now requires only one registry entry plus the asset files at the conventional paths (`assets/css/{slug}.css`, optional `assets/js/{slug}.js`)
- **Normalized asset handle naming** — every widget exposes a single conventional handle `paradise-{slug}` shared by its CSS and JS (e.g. `paradise-bottom-nav`). The previous mixed `-style` / `-script` suffix pattern has been removed
- **Minimum PHP raised from 7.4 to 8.0** — matches the codebase, which already used the `mixed` parameter type (PHP 8.0+) and typed properties

### Added

- **Elementor compatibility admin notices** — dismissible warning when Elementor is not active (`plugins_loaded:20` check) and a second one when the active Elementor is older than `PARADISE_EW_MIN_ELEMENTOR_VERSION` (3.5.0). Widget and asset registration is skipped on outdated Elementor so a Widget_Base API drift surfaces as a notice rather than a fatal

### Fixed

- **Bottom Navigation Bar** — corrected the asset handles inside `get_style_depends()` / `get_script_depends()` so the widget's CSS and JS actually load on the frontend. The widget asked for `paradise-bn-bottom-nav-style` / `-script`, but the main file registered `paradise-bottom-nav-style` / `-script`; the mismatch left the widget unstyled and non-interactive on production
- **Phone Link** — removed dead `get_uwidget_type()` method, a typo of a non-existent Elementor method (`Widget_Base::get_widget_type` does not exist) that was never called anywhere in the codebase
- **Floating Call Button** — corner offsets (`top` / `right` / `bottom` / `left`) were applied to the outer `.elementor-element` wrapper via `prefix_class`, but `position: fixed` lives on the inner `.paradise-fcb-wrap`. The offsets were ignored on the outer (static-positioned) wrapper and the inner fixed element fell back to its document-flow position. Corner CSS now targets the descendant `.paradise-fcb-wrap` so the button pins to the chosen corner with the configured offsets
- **FAQ Accordion** — closed items leaked ~28 px of the first answer line because `grid-template-rows: 0fr` was being overridden by the implicit min-content track sizing (the answer's inner padding). Switched to `minmax(0, 0fr)` so the collapsed track really shrinks to 0
- **FAQ Accordion** — `TypeError: Cannot read properties of undefined (reading 'addAction')` on templates where `elementorFrontend` loaded before its `hooks` API was ready (e.g. `elementor_canvas`). The editor re-init now checks both `elementorFrontend` and `elementorFrontend.hooks` before calling `addAction`

### Documentation

- **README Screenshots section** — added seven viewport-correct screenshots (mobile 390×844 and desktop 1280×800) for Bottom Navigation Bar, Floating Call Button, Announcement Bar, FAQ Accordion, Off-Canvas Menu (open state), Business Hours with the Open Now badge, and Author Card

---

## [2.4.0] - 2026-04-18

### Added

- **FAQ Accordion** — collapsible Q&A list with accordion mode (one item open at a time) or multi-expand mode; Elementor ICONS picker for open/closed state; left/right icon position; open-first-item default; Schema.org FAQPage JSON-LD output for Google rich results; full typography, color, padding, border, and shadow style controls
- **FAQ Post Type** — each post represents a "FAQ Set" (e.g. "General FAQ", "Pricing FAQ") with unlimited Q&A items stored in `_paradise_faq_items` post meta; TinyMCE rich text editor (bold, italic, links, lists) with controlled height and vertical scrollbar for answer editing; admin list shows item count and first question preview; toggled via the `faq_cpt` feature flag in plugin settings

### Fixed

- Elementor editor CSS appearing as visible text content in the canvas when the FAQ Accordion widget (CPT source) was present — root cause was `apply_filters('the_content', …)` being called inside widget render, which triggered Elementor's content pipeline and flushed the page CSS inline
- `date` orderby in FAQ `get_items()` was sorting ASC instead of DESC (newest first)

---

## [2.3.0] - 2026-04-17

### Added

- **Site Info** — new centralized data store (`paradise_site_info` option) for business name, phones, emails, addresses, social links, and business hours; exposes a `[paradise_site_info]` shortcode and Elementor Dynamic Tags
- **Business Hours** — displays site business hours from Site Info with a live "Open Now / Closed" badge; highlights today's row; supports 12 h / 24 h format; badge updates client-side using the site's timezone (independent of the visitor's browser timezone)
- **LocalBusiness Schema** — invisible widget that outputs Schema.org JSON-LD markup using Site Info data (name, phone, address, social sameAs, openingHoursSpecification); supports 14 Schema.org business type subtypes
- **Google Map** — embeds a Google Map via iframe; source can be the Site Info address (Map URL field) or a manual entry; supports border-radius, box-shadow, and height controls; Place and Directions modes; satellite / hybrid / terrain map types; zoom slider
- **Social Links** — row or column of social media icon links; source is Site Info socials or a custom repeater; supports brand / uniform colors, lift / scale / color-shift hover animations, circle / rounded shapes, and icon-only / icon+label / label-only display modes
- **Announcement Bar** — fixed full-width banner for announcements or promotions; supports icon, message, CTA button, and dismissal with session / days / permanent memory
- **Cookie Consent Bar** — GDPR/cookie consent bar with Accept and Decline buttons; stores user choice in localStorage with configurable expiry; dispatches consent events for analytics integration
- **Back to Top** — fixed-position button that appears after scrolling past a threshold and smoothly scrolls to the top of the page
- **Off-Canvas Menu** — slide-in side panel with a WordPress menu; triggered by an inline button or the `Paradise.openOffCanvas()` JS API (e.g. from Bottom Nav)
- **Sticky Header** — place inside any Elementor section to make it sticky; applies scroll effects (shadow, background change, shrink) when scrolling past a threshold
- Site Info: `map_url` field on each address entry (used by Google Map widget)
- Site Info: drag-to-reorder all repeater sections (phones, emails, addresses, socials) via jQuery UI Sortable
- Site Info: `label` attribute on `[paradise_site_info]` shortcode for matching by label instead of index
- Admin Settings: per-widget enable/disable toggles driven by a single `$widget_registry` array (single source of truth for settings UI, loading, and toggle logic)

### Changed

- `register_widgets()` refactored from manual `require_once` per widget to a compact foreach loop over `$widget_registry`; adding a new widget now requires only one registry entry (file + class keys)
- `$widget_registry` is now the single source of truth: each entry carries `label`, `description`, `file`, and `class`

### Fixed

- Google Map: URLs in `/maps/dir/` format (Directions) refused to load in iframes; now extracts the destination and rewrites to `maps.google.com/maps?q=...&output=embed`
- Google Map: `/maps/embed?q=ADDRESS` format ignored zoom; now rewritten to `maps.google.com/maps?q=...&z=...&output=embed` for reliable geocoding and zoom

---

## [2.2.0] - 2026-04-10

### Added

- WhatsApp link support in Phone Link widget (automatic country-code prefixing, `wa.me` URL format)
- WooCommerce cart count badge in Bottom Navigation Bar
- Schema.org Person markup on Author Card (improves SEO rich results)
- JS Hook system for Bottom Nav center button custom actions (`ebn:hook:{name}`)
- Custom CSS phone number mask for flexible display formats (e.g. `(###) ###-####`)

### Changed

- **BREAKING**: Bottom Navigation `get_name()` changed from `ebn_bottom_nav` to `paradise_bottom_nav` — existing widgets need a manual re-save in Elementor
- **BREAKING**: CSS class prefix for Bottom Nav updated to `paradise-bn-` for consistency

### Fixed

- Bottom Nav items no longer interfere with speed dial interactions
- Phone Link properly escapes all output for security
- Bottom Nav alignment cascade in Elementor editor

### Improved

- Editor preview uses `position: fixed` for pixel-perfect alignment inside iframe
- Responsive visibility respects Elementor's native breakpoints
- Speed Dial visible by default in editor for real-time feedback
- CSS class prefixes are now widget-specific for better maintainability

---

## [2.1.0] - 2026-04-10

### Added

- Elementor native responsive visibility for Bottom Navigation Bar (replaces custom breakpoint controls)
- Pixel-perfect editor preview — bar positions correctly inside Elementor iframe
- Speed dial auto-opens in editor for visual feedback
- `animEnabled` and `animStyle` controls for entrance animation

### Changed

- Rebranded from Glenar to Paradise
- PHP class renamed: `EBN_Widget` → `Glenar_Bottom_Nav_Widget` → `Paradise_Bottom_Nav_Widget`
- CSS constants renamed: `GLENAR_EW_*` → `PARADISE_EW_*`
- Phone Link CSS classes renamed: `.glenar-phone-*` → `.paradise-phone-*`

### Fixed

- Editor clicks disabled on nav items (standard Elementor behavior)
- Body padding uses `ResizeObserver` for accurate bar height tracking

---

## [2.0.0] - 2025-01-01

### Changed

- Removed all `!important` declarations from CSS
- CSS variables introduced for theming: `--ebn-bar-height`, `--ebn-anim-duration`

### Fixed

- Editor styles scoped to `body.elementor-editor-active` to prevent frontend bleed

---

## [1.0.0] - 2024-01-01

### Added

- Initial release
- Phone Link widget
- Bottom Navigation Bar widget (`ebn_bottom_nav`)
- Badge support: Static / WooCommerce Cart / JS-driven
- Speed Dial center button
- JS Hook system (`ebn:hook:{name}`)
- Active detection: URL match / Manual / Both
