# Paradise Elementor Widgets

Advanced custom Elementor widgets for WordPress — built for performance, flexibility, and pixel-perfect editor previews.

[![WordPress](https://img.shields.io/badge/WordPress-6.1%2B-blue)](https://wordpress.org)
[![Elementor](https://img.shields.io/badge/Elementor-3.5%2B-red)](https://elementor.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--2.0%2B-green)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Widgets

| Widget | Description |
|--------|-------------|
| [Phone Link](#-phone-link) | Formatted, linkable phone number with icon, prefix, and multiple display formats |
| [Bottom Navigation Bar](#-bottom-navigation-bar) | Mobile-first fixed navigation bar with badges, speed dial, and JS hooks |

---

## Requirements

| Requirement | Version |
|-------------|---------|
| WordPress | 6.1+ |
| Elementor | 3.5+ |
| PHP | 7.4+ |
| Elementor Pro | Optional (Theme Builder support) |

---

## Installation

1. Download or clone this repository into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone git@github.com:rezabagheri/paradise-elementor-widgets.git
   ```

2. Activate the plugin from **WordPress Admin → Plugins**.

3. Open Elementor editor — the **Paradise Widgets** category will appear in the widget panel.

---

## 📞 Phone Link

A smart phone number widget that normalizes any input format, builds proper `tel:` links, and renders with full layout control.

### Features

- **Phone number** with dynamic tag support
- **Prefix** with selectable HTML tag (H1–H6, p, div, span) and dynamic tag support
- **Layout modes:** Number Only / Prefix + Number / Icon + Number / Icon + Prefix + Number
- **Direction:** Inline or Stacked
- **Display formats:** Raw / International / Local / Dashes / Dots / Custom Mask
- **Country code:** US (+1), UK (+44), DE (+49), IR (+98), UAE (+971), or Custom
- **Link scope:** Full Widget / Number Only / No Link
- Automatically normalizes any phone input format → clean `tel:` href

### Display Format Examples

| Format | Output |
|--------|--------|
| Raw | As entered |
| International | +1 212 555 1234 |
| Local | (212) 555-1234 |
| Dashes | 212-555-1234 |
| Dots | 212.555.1234 |
| Custom Mask | `(###) ###-####` — use `#` for each digit |

### HTML Structure

```html
<div class="paradise-phone-link-wrapper">
  <a href="tel:+12125551234" class="paradise-phone-inner paradise-inline">
    <i class="paradise-phone-icon ..."></i>
    <span class="paradise-phone-prefix">Call Us:</span>
    <span class="paradise-phone-number">+1 212 555 1234</span>
  </a>
</div>
```

When **Link Scope = Number Only**:
```html
<div class="paradise-phone-link-wrapper">
  <div class="paradise-phone-inner paradise-inline">
    <a href="tel:+12125551234" class="paradise-phone-number-link">
      <span class="paradise-phone-number">+1 212 555 1234</span>
    </a>
  </div>
</div>
```

### CSS Classes

| Class | Description |
|-------|-------------|
| `.paradise-phone-link-wrapper` | Outer wrapper |
| `.paradise-phone-inner` | Inner flex container |
| `.paradise-phone-inline` | Inline direction modifier |
| `.paradise-phone-stacked` | Stacked direction modifier |
| `.paradise-phone-prefix` | Prefix element |
| `.paradise-phone-number` | Phone number span |
| `.paradise-phone-icon` | Icon element |
| `.paradise-phone-number-link` | Link wrapping number only (link scope = number) |

---

## 📱 Bottom Navigation Bar

A fully-featured mobile bottom navigation bar with responsive visibility, active state detection, badge support, speed dial, and a JavaScript hook system.

### Features

- **Items source:** Manual repeater or WordPress Menu
- **Badge support:** Static count / WooCommerce cart count / JS-driven via `EBN.setBadge()`
- **Responsive visibility:** Native Elementor responsive controls (Desktop / Tablet / Mobile)
- **Center button actions:** Link / Speed Dial / JS Hook
- **Active state detection:** URL match / Manual index / Both
- **URL match mode:** Pathname only / Full URL (with query string)
- **Active indicators:** None / Top Bar / Bottom Bar / Dot / Pill / Glow
- **Bar position:** Full Width / Floating Centered
- **Entrance animation:** Slide Up / Fade / Both — individually disableable
- **Editor preview:** Pixel-perfect fixed positioning inside the Elementor iframe

### HTML Structure

```html
<div class="ebn-wrapper ebn-pos-full" data-ebn="{...}">
  <div class="ebn-bar">

    <!-- Sliding indicator (top or bottom) -->
    <div class="ebn-indicator ebn-indicator--top_bar"></div>

    <!-- Nav item -->
    <a href="/page" class="ebn-item ebn-item--active" aria-current="page">
      <span class="ebn-item-icon">
        <i class="fas fa-home"></i>
        <span class="ebn-badge" data-ebn-badge-target="widget-id">3</span>
      </span>
      <span class="ebn-dot"></span>
      <span class="ebn-label">Home</span>
    </a>

    <!-- Center button (speed dial example) -->
    <div class="ebn-center-wrap">
      <button class="ebn-center-btn" data-ebn-action="speed_dial" aria-expanded="false">
        <span class="ebn-center-icon"><i class="fas fa-plus"></i></span>
      </button>
      <span class="ebn-center-label">More</span>

      <div class="ebn-speed-dial ebn-speed-dial--open" aria-hidden="false">
        <a href="/contact" class="ebn-dial-item">
          <span class="ebn-dial-icon"><i class="fas fa-phone"></i></span>
          <span class="ebn-dial-label">Contact</span>
        </a>
      </div>
    </div>

  </div>
</div>

<!-- Overlay (injected by JS, one per page) -->
<div class="ebn-overlay"></div>
```

### `data-ebn` Configuration

The widget passes its settings to JavaScript via a `data-ebn` JSON attribute:

```json
{
  "isEditMode": false,
  "detection": "both",
  "matchMode": "pathname",
  "manualIndex": 1,
  "indicator": "top_bar",
  "animated": true,
  "barPos": "full",
  "animEnabled": true,
  "animStyle": "slide_up",
  "animDuration": 350,
  "editorDialOpen": false
}
```

| Key | Values | Description |
|-----|--------|-------------|
| `detection` | `url` / `manual` / `both` | Active item detection strategy |
| `matchMode` | `pathname` / `full` | URL comparison scope |
| `manualIndex` | `1`-based integer | Fallback active item index |
| `indicator` | `top_bar` / `bot_bar` / `dot` / `pill` / `none` | Active indicator style |
| `animStyle` | `slide_up` / `fade` / `both` | Entrance animation |
| `animDuration` | milliseconds | Animation duration |

### JavaScript Public API

```javascript
// Set badge count programmatically
EBN.setBadge('your-widget-css-id', count);

// count = 0  → hides the badge
// count > 99 → shows "99+"
```

### JS Hook System

Assign **JS Hook** as the center button action in Elementor, then listen for the custom event:

```javascript
document.addEventListener('ebn:hook:myHookName', function (e) {
    console.log(e.detail.button);   // the button element
    console.log(e.detail.wrapper);  // the .ebn-wrapper element
});
```

### CSS Variables

Set automatically by JavaScript at runtime:

| Variable | Description |
|----------|-------------|
| `--ebn-bar-height` | Actual bar height (used for `body` bottom padding) |
| `--ebn-anim-duration` | Animation duration from widget settings |
| `--ebn-editor-bottom` | Editor-only offset for iframe positioning |

### Backward Compatibility

The following identifiers are permanent and must never change:

| Identifier | Value | Reason |
|------------|-------|--------|
| `get_name()` | `ebn_bottom_nav` | Elementor stores widget type in the database |
| CSS handle | `ebn-style` | May be enqueued/dequeued by third-party code |
| JS handle | `ebn-script` | May be enqueued/dequeued by third-party code |
| All control IDs | Unchanged | Elementor saves control values by ID |

---

## Developer Guide

### Adding a New Widget

**1.** Create `widgets/class-paradise-{name}.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Paradise_{Name}_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'paradise_{name}'; }
    public function get_title()      { return 'Paradise {Name}'; }
    public function get_icon()       { return 'eicon-...'; }
    public function get_categories() { return [ 'paradise' ]; }

    protected function register_controls() { /* ... */ }
    protected function render() { /* ... */ }
}
```

**2.** Register in `paradise-elementor-widgets.php` inside `register_widgets()`:

```php
require_once PARADISE_EW_DIR . 'widgets/class-paradise-{name}.php';
$widgets_manager->register( new Paradise_{Name}_Widget() );
```

**3.** If the widget has its own CSS/JS, register in `enqueue_assets()`:

```php
wp_register_style( 'paradise-{name}', PARADISE_EW_URL . 'assets/css/{name}.css', [], PARADISE_EW_VERSION );
```

Then declare it in the widget:

```php
public function get_style_depends(): array {
    return [ 'paradise-{name}' ];
}
```

### Constants

| Constant | Value |
|----------|-------|
| `PARADISE_EW_VERSION` | Current plugin version |
| `PARADISE_EW_DIR` | Absolute path to plugin directory (trailing slash) |
| `PARADISE_EW_URL` | URL to plugin directory (trailing slash) |

### Coding Conventions

- PHP 7.4+ — typed return types, arrow functions where appropriate
- No `!important` in CSS
- CSS variables for theming (`--ebn-*`)
- Vanilla JS only — no jQuery dependency
- JS public API on `window.EBN`
- Always escape output: `esc_html()`, `esc_url()`, `esc_attr()`
- Editor styles scoped to `body.elementor-editor-active`

---

## License

[GPL-2.0+](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Author

**Reza Bagheri** — [paradisecyber.com](https://www.paradisecyber.com)
