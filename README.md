# Paradise Elementor Widgets

**Advanced custom Elementor widgets** focused on mobile experience, direct contact, local SEO, and high performance.

[![WordPress](https://img.shields.io/badge/WordPress-6.1%2B-blue.svg)](https://wordpress.org)
[![Elementor](https://img.shields.io/badge/Elementor-3.5%2B-orange.svg)](https://elementor.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg)](https://www.php.net)
[![Version](https://img.shields.io/badge/Version-2.4.0-green.svg)](https://github.com/rezabagheri/paradise-elementor-widgets/releases)
[![License](https://img.shields.io/badge/License-GPL--2.0%2B-green.svg)](LICENSE)

---

### Features at a Glance

- **Performance Optimized** — No jQuery (except admin), scoped CSS, assets registered and enqueued per widget
- **Mobile-First** — Special focus on mobile navigation and UX
- **Pixel-Perfect Editor Preview** — All widgets render correctly inside the Elementor iframe
- **Full Dynamic Tags Support** — Phone, address, URLs, and more can be driven by ACF, post meta, or Site Info tags
- **Site Info** — Centralized business data store (phones, emails, addresses, socials, hours) shared across all widgets
- **Local SEO Ready** — LocalBusiness Schema widget outputs JSON-LD for rich Google results
- **No Build Step** — Plain PHP/CSS/JS, no npm required for production use

---

## Available Widgets

| Widget | Description |
| --- | --- |
| **Phone Link** | Clickable phone number with icon, prefix, format options, and `tel:`/WhatsApp link |
| **Phone Button** | Fully-styled CTA button for phone calls or WhatsApp |
| **Floating Call Button** | Fixed-position call/WhatsApp button with pulse animation and corner positioning |
| **Bottom Navigation Bar** | Fixed mobile bottom bar with icons, labels, badges, speed dial, and JS API |
| **Author Card** | Author profile card with photo, credentials, bio, custom fields, and CTA button |
| **Announcement Bar** | Fixed full-width banner with icon, message, CTA, and dismissal memory |
| **Cookie Consent Bar** | GDPR consent bar with Accept/Decline, localStorage expiry, and analytics events |
| **Back to Top** | Fixed button that appears on scroll and returns user to top |
| **Off-Canvas Menu** | Slide-in panel with a WordPress menu, triggerable by button or JS API |
| **Sticky Header** | Makes any Elementor section sticky with scroll shadow/shrink/background effects |
| **Google Map** | Google Map embed via iframe with Place and Directions modes, map types, and zoom |
| **Social Links** | Row/column of social media icon links with brand colors and hover animations |
| **Business Hours** | Business hours from Site Info with a live Open Now / Closed badge |
| **LocalBusiness Schema** | Invisible widget that injects Schema.org JSON-LD for local SEO |
| **FAQ Accordion** | Collapsible Q&A list with accordion or multi-expand mode, icon picker, and Schema.org FAQPage JSON-LD |

---

## Site Info

Site Info is the centralized business data store shared across all widgets. Configure it once under **Paradise → Elementor Widgets** in WordPress admin.

**Stored data:**

- Business name
- Phone numbers (with labels)
- Email addresses (with labels)
- Physical addresses (with labels and Google Map URL)
- Social media links (platform + URL)
- Business hours (per day, open/closed, from/to)

Phone Link, Google Map, Social Links, Business Hours, and LocalBusiness Schema all read from Site Info automatically.

**Shortcode:**

```
[paradise_site_info type="phone"]
[paradise_site_info type="email" index="1"]
[paradise_site_info type="address" label="Main Office"]
[paradise_site_info type="address_map" index="0"]
```

**Dynamic Tags:** Available in Elementor for phone, email, address, address map URL, and social URL fields.

---

## Requirements

- WordPress 6.1 or higher
- Elementor 3.5 or higher (free version is sufficient)
- PHP 7.4 or higher
- Elementor Pro (optional — for Theme Builder and Dynamic Tags on Pro controls)

---

## Installation

1. Clone the repository into your WordPress plugins directory:

```bash
cd wp-content/plugins/
git clone https://github.com/rezabagheri/paradise-elementor-widgets.git
```

2. Activate the plugin from **WordPress Admin → Plugins**.
3. Open Elementor editor — the **Paradise Widgets** category will appear.

---

## File Structure

```
paradise-elementor-widgets/
├── paradise-elementor-widgets.php    # Main plugin file — bootstraps everything
├── admin/
│   ├── class-paradise-ew-admin.php   # Widget registry, settings, menus
│   ├── class-paradise-site-info-admin.php
│   ├── class-paradise-user-profile.php
│   └── views/
│       ├── page-settings.php         # Widget toggle UI
│       └── page-site-info.php        # Site Info editor UI
├── includes/
│   ├── class-paradise-site-info.php  # Site Info data model + shortcode
│   ├── class-paradise-dynamic-tags.php
│   ├── class-paradise-faq-cpt.php    # FAQ Post Type — sets + TinyMCE meta box
│   └── trait-paradise-phone-helper.php
├── widgets/                          # One file per widget
│   ├── class-paradise-phone-link.php
│   ├── class-paradise-phone-button.php
│   ├── class-paradise-floating-call-btn.php
│   ├── class-paradise-bottom-nav.php
│   ├── class-paradise-author-card.php
│   ├── class-paradise-announcement-bar.php
│   ├── class-paradise-cookie-consent-bar.php
│   ├── class-paradise-back-to-top.php
│   ├── class-paradise-off-canvas-menu.php
│   ├── class-paradise-sticky-header.php
│   ├── class-paradise-google-map.php
│   ├── class-paradise-social-links.php
│   ├── class-paradise-business-hours.php
│   ├── class-paradise-local-business-schema.php
│   └── class-paradise-faq-accordion.php
└── assets/
    ├── css/                          # One CSS file per widget
    └── js/                           # JS only for widgets that need it
```

---

## Developer Guide

### Adding a new widget

1. Create `widgets/class-paradise-{slug}.php` with a class extending `\Elementor\Widget_Base`
2. Add one entry to `$widget_registry` in `admin/class-paradise-ew-admin.php`:

```php
'my_widget' => [
    'label'       => 'My Widget',
    'description' => 'Short description for the settings toggle page.',
    'file'        => 'widgets/class-paradise-my-widget.php',
    'class'       => 'Paradise_My_Widget',
],
```

3. Register CSS/JS handles in `enqueue_assets()` in the main plugin file (use `wp_register_*`, not `wp_enqueue_*` — each widget's `get_style_depends()` / `get_script_depends()` handles enqueueing).

No other changes needed — the registry drives settings, toggle UI, and loading automatically.

### Constants

| Constant | Value |
| --- | --- |
| `PARADISE_EW_VERSION` | Current plugin version |
| `PARADISE_EW_DIR` | Absolute path to plugin directory (trailing slash) |
| `PARADISE_EW_URL` | URL to plugin directory (trailing slash) |

### Code conventions

- PHP 7.4+ — typed return types, arrow functions where appropriate
- No `!important` in CSS
- CSS class prefix is widget-specific (e.g. `paradise-bn-*`, `paradise-ab-*`)
- All output escaped: `esc_html`, `esc_url`, `esc_attr`
- JS: vanilla, no jQuery dependency, IIFE pattern

---

## Version History

### v2.4.0 (April 2026)

- FAQ Accordion widget — accordion/multi-expand, Elementor icon picker (closed/open), icon position, Schema.org FAQPage JSON-LD, full style controls
- FAQ Post Type — each post is a "FAQ Set" with unlimited Q&A items stored in meta; TinyMCE rich text editor for answers; toggle on/off in plugin settings
- Fixed: Elementor editor CSS appearing as visible text when FAQ CPT source was active (caused by `apply_filters('the_content', …)` inside widget render)

### v2.3.0 (April 2026)

- Site Info centralized data store with shortcode and Dynamic Tags
- 9 new widgets: Business Hours, LocalBusiness Schema, Google Map, Social Links, Announcement Bar, Cookie Consent Bar, Back to Top, Off-Canvas Menu, Sticky Header
- Widget registry as single source of truth — adding a widget now requires one registry entry

### v2.2.0 (April 2026)

- WhatsApp support in Phone Link and Phone Button
- WooCommerce cart badge in Bottom Nav
- Schema.org Person markup on Author Card
- JS Hook system for Bottom Nav center button

### v2.1.0 (April 2026)

- Rebranded from Glenar to Paradise
- Elementor native responsive visibility for Bottom Nav
- Pixel-perfect editor preview

### v2.0.0 (January 2025)

- Removed all `!important` from CSS
- CSS variables for theming

### v1.0.0 (January 2024)

- Initial release: Phone Link, Bottom Navigation Bar

---

## Support & Contact

- **Issues**: [GitHub Issues](https://github.com/rezabagheri/paradise-elementor-widgets/issues)
- **Website**: [https://paradisecyber.com](https://paradisecyber.com)
- **Email**: rezabagheri@gmail.com

---

## License

Licensed under the **GPL-2.0+** license. See the [LICENSE](LICENSE) file for details.
