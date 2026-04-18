# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
