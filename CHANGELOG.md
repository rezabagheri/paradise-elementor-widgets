# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
