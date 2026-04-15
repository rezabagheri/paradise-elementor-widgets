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
| [Author Card](#-author-card) | Professional author/profile card with photo, bio, social links, and custom meta fields |
| [Phone Link](#-phone-link) | Formatted, linkable phone number with icon, prefix, and multiple display formats |
| [Phone Button](#-phone-button) | Styled CTA button that dials phone number or opens WhatsApp |
| [Floating Call Button](#-floating-call-button) | Fixed-position button that stays visible while scrolling — call or WhatsApp |
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

## � Author Card

A professional profile card widget that displays author information with photo, name, title, bio, and custom fields.

### Features

- **Photo source:** Paradise meta / Gravatar / Custom meta key / Static image upload
- **Name & title** with optional link
- **Bio/description** with rich text support
- **Custom fields repeater:** Text, Link, Email, or Badge type
- **Social links** with icon-only or icon+label display
- **CTA button** with full styling
- **Layout:** Vertical or Horizontal
- **Alignment:** Left / Center / Right — cascades to all sections
- **Schema.org Person markup** for SEO

### HTML Structure

```html
<div class="paradise-author-card" itemscope itemtype="https://schema.org/Person">
  <div class="paradise-author-card__photo-wrap">
    <a class="paradise-author-card__photo-link" href="#">
      <img class="paradise-author-card__photo" itemprop="image" src="..." alt="..." />
    </a>
  </div>

  <div class="paradise-author-card__body">
    <h3 class="paradise-author-card__name" itemprop="name">John Doe</h3>
    <p class="paradise-author-card__title" itemprop="jobTitle">Designer</p>
    <p class="paradise-author-card__bio" itemprop="description">Bio text...</p>

    <div class="paradise-author-card__fields">
      <div class="paradise-author-card__field">
        <span class="paradise-author-card__field-label">Phone:</span>
        <a class="paradise-author-card__field-link" href="tel:...">+1 234 567 8900</a>
      </div>
    </div>

    <div class="paradise-author-card__social">
      <a class="paradise-author-card__social-link" itemprop="sameAs" href="#">
        <i class="paradise-author-card__social-icon fas fa-linkedin"></i>
      </a>
    </div>

    <a class="paradise-author-card__btn" itemprop="url" href="#">Learn More</a>
  </div>
</div>
```

---

## �📞 Phone Link

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

## � Phone Button

A styled CTA button that triggers phone calls or WhatsApp messages.

### Features

- **Phone number** with full normalization and formatting
- **Button text** with dynamic tag support
- **Display formats:** Same as Phone Link (Raw / International / Local / Dashes / Dots)
- **Link type:** Phone Call (`tel:`) or WhatsApp (`https://wa.me/{digits}`)
- **Icon** with position and color controls
- **Full button styling:** Background, border, shadow, hover effects
- **Responsive size & padding**

### HTML Structure

```html
<a href="tel:+12125551234" class="paradise-phone-button">
  <i class="paradise-phone-button__icon fas fa-phone"></i>
  <span class="paradise-phone-button__text">Call Now</span>
</a>
```

### CSS Classes

| Class | Description |
|-------|-------------|
| `.paradise-phone-button` | Main button link |
| `.paradise-phone-button__icon` | Icon element |
| `.paradise-phone-button__text` | Button text span |
| `.paradise-phone-button:hover` | Hover state |

---

## ☎️ Floating Call Button

A fixed-position button that remains visible while users scroll. Perfect for persistent call-to-action.

### Features

- **Phone number** with formatting
- **Label text** (optional)
- **Display formats:** Raw / International / Local / Dashes / Dots / Custom Mask
- **Link type:** Phone Call or WhatsApp
- **Position:** Any corner (Top-Left, Top-Right, Bottom-Left, Bottom-Right) with custom offset
- **Pulse animation:** On/Off with customizable speed
- **Icon** with size and color controls
- **Full button styling:** Background, border, shadow, animations
- **Responsive visibility:** Show/hide on different breakpoints

### HTML Structure

```html
<div class="paradise-floating-call-btn paradise-fcb-bottom-right">
  <a href="tel:+12125551234" class="paradise-fcb-button">
    <i class="paradise-fcb-icon fas fa-phone"></i>
  </a>
  <div class="paradise-fcb-pulse"></div>
  <span class="paradise-fcb-label">Call Us</span>
</div>
```

### CSS Classes

| Class | Description |
|-------|-------------|
| `.paradise-floating-call-btn` | Main container (fixed) |
| `.paradise-fcb-bottom-right`, etc. | Position variant |
| `.paradise-fcb-button` | Button element |
| `.paradise-fcb-icon` | Icon inside button |
| `.paradise-fcb-pulse` | Pulse animation overlay |
| `.paradise-fcb-label` | Optional label text |

---

## �📱 Bottom Navigation Bar

A fully-featured mobile bottom navigation bar with responsive visibility, active state detection, badge support, speed dial, and a JavaScript hook system.

### Features

- **Items source:** Manual repeater or WordPress Menu
- **Badge support:** Static count / WooCommerce cart count / JS-driven via `Paradise.setBadge()`
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
<div class="paradise-wrapper paradise-pos-full" data-paradise="{...}">
  <div class="paradise-bar">

    <!-- Sliding indicator (top or bottom) -->
    <div class="paradise-indicator paradise-indicator--top_bar"></div>

    <!-- Nav item -->
    <a href="/page" class="paradise-item paradise-item--active" aria-current="page">
      <span class="paradise-item-icon">
        <i class="fas fa-home"></i>
        <span class="paradise-badge" data-paradise-badge-target="widget-id">3</span>
      </span>
      <span class="paradise-dot"></span>
      <span class="paradise-label">Home</span>
    </a>

    <!-- Center button (speed dial example) -->
    <div class="paradise-center-wrap">
      <button class="paradise-center-btn" data-paradise-action="speed_dial" aria-expanded="false">
        <span class="paradise-center-icon"><i class="fas fa-plus"></i></span>
      </button>
      <span class="paradise-center-label">More</span>

      <div class="paradise-speed-dial paradise-speed-dial--open" aria-hidden="false">
        <a href="/contact" class="paradise-dial-item">
          <span class="paradise-dial-icon"><i class="fas fa-phone"></i></span>
          <span class="paradise-dial-label">Contact</span>
        </a>
      </div>
    </div>

  </div>
</div>

<!-- Overlay (injected by JS, one per page) -->
<div class="paradise-overlay"></div>
```

### `data-paradise` Configuration

The widget passes its settings to JavaScript via a `data-paradise` JSON attribute:

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
Paradise.setBadge('your-widget-css-id', count);

// count = 0  → hides the badge
// count > 99 → shows "99+"
```

### JS Hook System

Assign **JS Hook** as the center button action in Elementor, then listen for the custom event:

```javascript
document.addEventListener('ebn:hook:myHookName', function (e) {
    console.log(e.detail.button);   // the button element
    console.log(e.detail.wrapper);  // the .paradise-wrapper element
});
```

### CSS Variables

Set automatically by JavaScript at runtime:

| Variable | Description |
|----------|-------------|
| `--paradise-bar-height` | Actual bar height (used for `body` bottom padding) |
| `--paradise-anim-duration` | Animation duration from widget settings |
| `--paradise-editor-bottom` | Editor-only offset for iframe positioning |

### Backward Compatibility

The following identifiers are permanent and must never change:

| Identifier | Value | Reason |
|------------|-------|--------|
| `get_name()` | `ebn_bottom_nav` | Elementor stores widget type in the database |
| CSS handle | `paradise-style` | May be enqueued/dequeued by third-party code |
| JS handle | `paradise-script` | May be enqueued/dequeued by third-party code |
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
- CSS variables for theming (`--paradise-*`)
- Vanilla JS only — no jQuery dependency
- JS public API on `window.EBN`
- Always escape output: `esc_html()`, `esc_url()`, `esc_attr()`
- Editor styles scoped to `body.elementor-editor-active`

---

## Font Icons

All widgets use **FontAwesome** for icons. Ensure FontAwesome is available on your site:

- **WordPress FontAwesome Plugin** (Recommended) — auto-registers all icons
- **Manual:** Link FontAwesome CSS in your theme's `functions.php`:
  ```php
  wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' );
  ```

Elementor's built-in icon picker will show all available icons.

---

## Changelog

### Version 2.2.0 (April 2026)

**Breaking Changes:**
- Bottom Navigation widget `get_name()` changed from `ebn_bottom_nav` to `paradise_bottom_nav`
  - Existing widgets will need manual re-save in Elementor after update
- CSS class prefix for Bottom Nav updated: `paradise-bn-` (was generic `paradise-`)

**Improvements:**
- ✅ Editor preview now uses `position: fixed` inside iframe (pixel-perfect alignment with frontend)
- ✅ Bottom Nav responsive visibility now respects Elementor's native breakpoints
- ✅ Speed Dial visible by default in editor for real-time feedback
- ✅ CSS class prefixes now widget-specific for better maintainability
- ✅ Added Schema.org markup to Author Card (`itemprop` attributes)

**New Features:**
- WhatsApp link support in Phone Link widget (`https://wa.me/{digits}`)
- WooCommerce cart count badge in Bottom Nav
- JS Hook system for center button actions
- Custom CSS mask for phone number display

**Bug Fixes:**
- Fixed Bottom Nav items interference with speed dial interaction
- Phone Link now properly escapes output
- Fixed alignment cascade in Author Card alignment control

---

## Development Setup

### Local Development

1. **Clone the repository:**
   ```bash
   cd wp-content/plugins/
   git clone git@github.com:rezabagheri/paradise-elementor-widgets.git
   cd paradise-elementor-widgets
   ```

2. **Activate the plugin:**
   ```bash
   wp plugin activate paradise-elementor-widgets
   ```

3. **Open Elementor or Page Builder** to preview widget changes

### Making Changes

- **Widgets:** Edit files in `widgets/class-paradise-*.php`
- **Styles:** Edit CSS files in `assets/css/` — reload page in browser (no build step)
- **Scripts:** Edit `assets/js/bottom-nav.js` — reload page
- **Admin:** Edit `admin/class-paradise-*.php` and `admin/views/page-settings.php`

### Before Committing

```bash
# Add only changed files
git add -A

# Commit with meaningful message
git commit -m "fix: editor preview alignment issue
- Added position: fixed to .paradise-bn-wrapper
- Maintains pixel-perfect preview inside iframe"

# Push to your branch
git push origin feature/branch-name
```

### Recommended Developer Extensions

- **WordPress Debugging:** Add to `wp-config.php`:
  ```php
  define( 'WP_DEBUG', true );
  define( 'WP_DEBUG_LOG', true );
  define( 'WP_DEBUG_DISPLAY', false );
  ```
  Then check `/wp-content/debug.log`

- **Browser DevTools:** Inspect element styles, check `data-paradise` JSON on Bottom Nav

---

## Troubleshooting

### Bottom Navigation Bar not showing

**Check:**
1. Elementor responsive settings — ensure widget is set to display on your breakpoint
2. Browser DevTools: Check if `.paradise-bn-wrapper` has `display: block;` (should not be `none`)
3. Check CSS file is loaded: DevTools → Network → filter `bottom-nav.css` should be green (200 OK)

**Solution:**
- Ensure Bottom Nav is placed in a page widget, not floating inside another widget
- Check `body` does not have extreme `overflow: hidden` that cuts off the bar

### Phone Link not formatting correctly

**Check:**
1. Phone number contains only digits or valid country code prefix (e.g., `+1212555...`)
2. Selected country code matches the number (e.g., US +1 for `212` area code)
3. Custom Mask contains only `#` symbols (e.g., `(###) ###-####`)

**Solution:**
- Use "Raw" format to verify raw input is correct
- Try a different country code if number seems invalid
- Clear browser cache (Ctrl+Shift+Delete / Cmd+Shift+Delete)

### Editor preview doesn't match frontend

**Check:**
1. Browser zoom is 100% (Cmd/Ctrl+0)
2. Bottom Nav CSS file is loaded: `bottom-nav.css` in Network tab
3. Both editor iframe and frontend are using same breakpoint (Mobile/Tablet/Desktop)

**Solution:**
- Hard refresh: Cmd/Ctrl+Shift+R (bypass cache)
- Check Elementor version: must be 3.5+
- Disable browser extensions (ad blockers can interfere with Elementor)

### WooCommerce badge not updating

**Check:**
1. WooCommerce is installed and active
2. Badge setting is set to "WooCommerce Cart" (not Static)
3. `WC()->cart` is available (cart must be instantiated)

**Solution:**
- Reload page after adding items to cart
- Check cart page shows correct count
- If still broken, verify WooCommerce is properly installed

### Speed Dial items not opening

**Check:**
1. Center button action is set to "Speed Dial"
2. At least one speed dial item is added in repeater
3. JavaScript is not blocked (check Network tab for `bottom-nav.js`)

**Solution:**
- Open browser console (F12): check for any red JS errors
- Ensure no other plugin is conflicting (disable temporarily to test)
- Verify Bottom Nav CSS has correct z-index values

---

## Known Issues & Limitations

| Issue | Status | Workaround |
|-------|--------|-----------|
| Bottom Nav with "Speed Dial" action may conflict with other fixed elements | v2.2.0 | Increase z-index value manually or adjust stacking context |
| Phone Link custom mask doesn't validate input | ✓ Feature | Currently allows any input; validation may be added in v2.3 |
| Editor preview requires iframe refresh after breakpoint change | ✓ Expected | Manually refresh Elementor canvas or toggle responsive mode |
| FontAwesome 5/6 compatibility | ✓ Tested | Both versions work; ensure one is loaded globally |
| Bottom Nav alignment with notched phones (safe-area) | ✓ CSS | Uses `env(safe-area-inset-bottom)` — works on iOS 11+ |

---

## Contributing

We welcome contributions! Please follow these guidelines:

1. **Create a feature branch:**
   ```bash
   git checkout -b feature/paradise-new-widget
   git checkout -b fix/editor-alignment
   git checkout -b docs/update-readme
   ```

2. **Commit messages should follow Conventional Commits:**
   ```
   feat: add CTA Button widget
   fix: editor preview alignment inside iframe
   docs: update README troubleshooting section
   style: remove unused CSS variables
   refactor: simplify phone number normalization
   ```

3. **Before pushing:**
   - Test in both editor and frontend
   - Verify mobile/tablet/desktop responsiveness
   - Check PHP 7.4+ syntax compatibility
   - Ensure no PHP warnings in debug log

4. **Submit pull requests to `develop` branch** (not `main`)

---

## Support

For issues, feature requests, or questions:

- **GitHub Issues:** [Create an issue](https://github.com/rezabagheri/paradise-elementor-widgets/issues)
- **Email:** rezabagheri@gmail.com
- **Website:** [paradisecyber.com](https://www.paradisecyber.com)

---

## Version History

| Version | Release Date | Status |
|---------|-------------|--------|
| 2.2.0 | April 2026 | Current |
| 2.1.0 | March 2026 | Archived |
| 2.0.0 | January 2026 | Archived |
| 1.0.0 | 2025 | Archived |

---

## License

[GPL-2.0+](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Author

**Reza Bagheri** — [paradisecyber.com](https://www.paradisecyber.com)

**Contact:** rezabagheri@gmail.com
