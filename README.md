# Paradise Elementor Widgets

**Advanced custom Elementor widgets** focused on mobile experience, direct contact, and high performance.

![Paradise Elementor Widgets](https://via.placeholder.com/1200x300/4F46E5/FFFFFF?text=Paradise+Elementor+Widgets)

[![WordPress](https://img.shields.io/badge/WordPress-6.1%2B-blue.svg)](https://wordpress.org)
[![Elementor](https://img.shields.io/badge/Elementor-3.5%2B-orange.svg)](https://elementor.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg)](https://www.php.net)
[![Version](https://img.shields.io/badge/Version-2.2.0-green.svg)](https://github.com/rezabagheri/paradise-elementor-widgets/releases)
[![License](https://img.shields.io/badge/License-GPL--2.0%2B-green.svg)](LICENSE)

---

### Features at a Glance

- ⚡ **Performance Optimized** — No jQuery, scoped CSS, smart asset loading
- 📱 **Mobile-First** — Special focus on mobile navigation and UX
- 🎨 **Pixel-Perfect Editor Preview**
- 🔗 **Full Dynamic Tags Support**
- 📊 **Schema.org Ready** — Author Card and future FAQ
- 🛒 **WooCommerce Integration** — Cart badge and mini cart support
- 🧩 **JavaScript API & Hooks** — Full external control
- 🛡️ **Secure & Clean Code** — Proper escaping, no !important

---

## Live Demo & Playground

- **Live Demo** → [https://demo.paradisecyber.com](https://demo.paradisecyber.com) *(Replace with your subdomain after creation)*
- **Widget Playground** → Interactive real-time testing of all widgets *(Coming soon)*

---

## Available Widgets

| Widget                    | Description                                                                 | Best For                          |
|---------------------------|-----------------------------------------------------------------------------|-----------------------------------|
| **Author Card**           | Professional author/team card with image, bio, custom fields and Schema.org | Blog, About Us, Team pages        |
| **Phone Link**            | Smart phone number with masking, country code and tel: link                 | Contact sections, landing pages   |
| **Phone Button**          | Stylish call or WhatsApp button                                             | CTAs, service pages               |
| **Floating Call Button**  | Fixed floating button with pulse animation and custom positioning           | All websites, especially mobile   |
| **Bottom Navigation Bar** | Advanced mobile bottom navigation with badge, Speed Dial, active page detection and JS Hooks | Online stores, app-like websites  |

---

## Requirements

- WordPress 6.1 or higher
- Elementor 3.5 or higher
- PHP 7.4 or higher
- Elementor Pro (optional — for Theme Builder & Dynamic Tags)

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

## Widget Documentation

### Author Card
Professional author or team member card featuring:
- Multiple image sources (Meta, Gravatar, Custom Field, Static)
- Rich bio and custom fields repeater
- Social links and CTA button
- Vertical / Horizontal layouts
- Full Schema.org markup

**Use Cases**: About page, blog posts, team section

### Phone Link
Intelligent phone number formatting for Iran and international numbers:
- Custom mask, country code, multiple formats
- Full Dynamic Tags support
- Smart tel: link generation

**Use Cases**: Header, footer, service landing pages

### Phone Button & Floating Call Button
Beautiful call or WhatsApp buttons with:
- Pulse animation, fixed positioning, responsive visibility
- WhatsApp support with pre-filled message
- Full styling options (color, shadow, hover)

**Use Cases**: Increase call and conversion rate on mobile

### Bottom Navigation Bar
One of the most advanced mobile navigation widgets:
- Repeater or WordPress menu source
- Static, WooCommerce or JS-based badge
- Speed Dial with smooth animation
- Active page detection
- Powerful JavaScript API and Hook System

**JavaScript API Example:**

```javascript
Paradise.setBadge('bottom-nav-widget', 5);

// Hook for center button
document.addEventListener('ebn:hook:myAction', (e) => {
    console.log('Center button clicked!', e.detail);
});
```

**Use Cases**: E-commerce stores, service websites, turning website into app-like experience

---

## Developer Guide

- To add a new widget, create `class-paradise-{slug}.php` in the `widgets/` folder and register it.
- All assets are enqueued only when needed.
- Use the following constants:
  - `PARADISE_EW_VERSION`
  - `PARADISE_EW_URL`
  - `PARADISE_EW_DIR`

A base widget class will be added in the next major update for better consistency.

---

## Roadmap

- **v2.3** — WhatsApp Chat Bubble + Advanced Scroll to Top
- **v2.4** — Smart FAQ with Schema.org + Floating Mini Cart
- **v2.5** — Global Widget Settings Panel + Extended JS API

---

## Changelog

See the full changelog in [CHANGELOG.md](CHANGELOG.md).

**v2.2.0 (April 2026)**
- Full WhatsApp support added
- Major improvements to Bottom Navigation (Speed Dial + Hooks)
- Better Schema.org and output escaping

---

## Contributing

Contributions are welcome!
Please use Conventional Commits and submit Pull Requests to the `develop` branch.

---

## Support & Contact

- **Issues**: [GitHub Issues](https://github.com/rezabagheri/paradise-elementor-widgets/issues)
- **Website**: [https://paradisecyber.com](https://paradisecyber.com)
- **Email**: rezabagheri@gmail.com

---

## License

This project is licensed under the **GPL-2.0+** license.
See the [LICENSE](LICENSE) file for details.

---

Thank you for using **Paradise Elementor Widgets** ❤️
Any feedback or suggestions are highly appreciated!
