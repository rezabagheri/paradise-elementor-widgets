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

## Development Setup

### Prerequisites
- **WordPress**: 6.1+
- **Elementor**: 3.5+
- **PHP**: 7.4+
- **Node.js**: 16+ (for development)
- **Composer**: For PHP dependencies

### Local Development
1. Clone the repository:
```bash
git clone https://github.com/rezabagheri/paradise-elementor-widgets.git
cd paradise-elementor-widgets
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies (if building assets):
```bash
npm install
```

4. Activate the plugin in WordPress and start developing.

### File Structure
```
paradise-elementor-widgets/
├── paradise-elementor-widgets.php    # Main plugin file
├── admin/                            # Admin settings
├── includes/                         # Shared traits
├── widgets/                          # Widget classes
├── assets/
│   ├── css/                          # Stylesheets
│   └── js/                           # JavaScript files
└── README.md
```

### Building Assets
```bash
# Compile CSS/JS (if using build tools)
npm run build

# Watch for changes during development
npm run watch
```

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

**HTML Structure:**
```html
<div class="paradise-author-card" itemscope itemtype="https://schema.org/Person">
  <div class="paradise-author-card__photo-wrap">
    <img class="paradise-author-card__photo" itemprop="image" src="..." alt="..." />
  </div>
  <div class="paradise-author-card__body">
    <h3 class="paradise-author-card__name" itemprop="name">John Doe</h3>
    <p class="paradise-author-card__title" itemprop="jobTitle">Designer</p>
    <p class="paradise-author-card__bio" itemprop="description">Bio text...</p>
    <a class="paradise-author-card__btn" itemprop="url" href="#">Learn More</a>
  </div>
</div>
```

### Phone Link
Intelligent phone number formatting for Iran and international numbers:
- Custom mask, country code, multiple formats
- Full Dynamic Tags support
- Smart tel: link generation

**Use Cases**: Header, footer, service landing pages

**Display Format Examples:**
| Format | Output |
|--------|--------|
| Raw | As entered |
| International | +1 212 555 1234 |
| Local | (212) 555-1234 |
| Dashes | 212-555-1234 |
| Dots | 212.555.1234 |
| Custom Mask | `(###) ###-####` |

**HTML Structure:**
```html
<div class="paradise-phone-link-wrapper">
  <a href="tel:+12125551234" class="paradise-phone-inner paradise-inline">
    <i class="paradise-phone-icon fas fa-phone"></i>
    <span class="paradise-phone-prefix">Call Us:</span>
    <span class="paradise-phone-number">+1 212 555 1234</span>
  </a>
</div>
```

**CSS Classes:**
| Class | Description |
|-------|-------------|
| `.paradise-phone-link-wrapper` | Outer wrapper |
| `.paradise-phone-inner` | Inner flex container |
| `.paradise-phone-inline` | Inline direction modifier |
| `.paradise-phone-stacked` | Stacked direction modifier |
| `.paradise-phone-prefix` | Prefix element |
| `.paradise-phone-number` | Phone number span |
| `.paradise-phone-icon` | Icon element |
| `.paradise-phone-number-link` | Link wrapping number only |

### Phone Button & Floating Call Button
Beautiful call or WhatsApp buttons with:
- Pulse animation, fixed positioning, responsive visibility
- WhatsApp support with pre-filled message
- Full styling options (color, shadow, hover)

**Use Cases**: Increase call and conversion rate on mobile

**Phone Button HTML:**
```html
<a href="tel:+12125551234" class="paradise-phone-button">
  <i class="paradise-phone-button__icon fas fa-phone"></i>
  <span class="paradise-phone-button__text">Call Now</span>
</a>
```

**Floating Call Button HTML:**
```html
<div class="paradise-floating-call-btn paradise-fcb-bottom-right">
  <a href="tel:+12125551234" class="paradise-fcb-button">
    <i class="paradise-fcb-icon fas fa-phone"></i>
  </a>
  <div class="paradise-fcb-pulse"></div>
  <span class="paradise-fcb-label">Call Us</span>
</div>
```

**CSS Classes:**
| Class | Description |
|-------|-------------|
| `.paradise-phone-button` | Main button link |
| `.paradise-phone-button__icon` | Icon element |
| `.paradise-phone-button__text` | Button text span |
| `.paradise-floating-call-btn` | Main container (fixed) |
| `.paradise-fcb-bottom-right` | Position variant |
| `.paradise-fcb-button` | Button element |
| `.paradise-fcb-icon` | Icon inside button |
| `.paradise-fcb-pulse` | Pulse animation overlay |
| `.paradise-fcb-label` | Optional label text |

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

**HTML Structure:**
```html
<div class="paradise-bn-wrapper paradise-bn-pos-full" data-paradise="{...}">
  <div class="paradise-bn-bar">
    <a href="/page" class="paradise-bn-item paradise-bn-item--active">
      <span class="paradise-bn-item-icon">
        <i class="fas fa-home"></i>
        <span class="paradise-bn-badge">3</span>
      </span>
      <span class="paradise-bn-label">Home</span>
    </a>
  </div>
</div>
```

**data-paradise Configuration:**
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

**CSS Variables:**
| Variable | Description |
|----------|-------------|
| `--paradise-bn-bar-height` | Actual bar height |
| `--paradise-bn-anim-duration` | Animation duration |
| `--paradise-bn-editor-bottom` | Editor-only offset |

---

## Font Icons

The plugin uses Font Awesome icons throughout the widgets. Make sure Font Awesome is loaded on your site.

### Required Font Awesome Classes
- `fas fa-home` - Home icon
- `fas fa-user` - User/Person icon
- `fas fa-phone` - Phone icon
- `fas fa-envelope` - Email icon
- `fas fa-globe` - Website icon
- `fas fa-star` - Rating/Star icon
- `fas fa-heart` - Favorite/Like icon
- `fas fa-share` - Share icon

### Loading Font Awesome
If Font Awesome is not already loaded, you can:

1. **Use a plugin**: Install "Font Awesome" or "Better Font Awesome" plugin
2. **Manual enqueue**: Add to your theme's `functions.php`:
```php
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
```
3. **CDN in header**: Add to your theme's `<head>`:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
```

### Custom Icons
You can override default icons using CSS:
```css
.paradise-author-card__social-link[href*="twitter"]::before {
  content: "\f099"; /* Twitter icon */
}
```

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

## Troubleshooting

### Bottom Navigation Bar not showing
**Check:** Elementor responsive settings and CSS file loading.

### Phone Link not formatting correctly
**Check:** Phone number format and country code selection.

### Editor preview doesn't match frontend
**Check:** Browser zoom and Elementor version compatibility.

**v2.2.0 (April 2026)**
- Full WhatsApp support added
- Major improvements to Bottom Navigation (Speed Dial + Hooks)
- Better Schema.org and output escaping

---

## Known Issues & Limitations

### Current Limitations
- **Bottom Navigation**: Requires JavaScript for full functionality
- **Phone Widgets**: Country detection limited to Iran and common international formats
- **Author Card**: Gravatar integration requires valid email addresses
- **Performance**: Multiple instances of the same widget on one page may impact load times

### Browser Compatibility
- **Supported**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Limited**: Internet Explorer 11 (degraded experience)
- **Not Supported**: Older browsers without CSS Grid support

### Elementor Compatibility
- **Fully Compatible**: Elementor Pro 3.5+
- **Compatible**: Elementor Free 3.5+ (with limitations on dynamic content)
- **Not Compatible**: Elementor versions below 3.5

### Troubleshooting
- **Widget not appearing**: Check Elementor version and PHP requirements
- **Styles not loading**: Clear WordPress and browser cache
- **JavaScript errors**: Check for plugin conflicts in browser console
- **Phone number formatting**: Verify country code settings in widget options

---

## Contributing

Contributions are welcome!
Please use Conventional Commits and submit Pull Requests to the `develop` branch.

---

## Version History

### v1.0.0 (Current)
- **Initial Release**
- Author Card widget with Schema.org markup
- Phone Link widget with intelligent formatting
- Phone Button widget with click-to-call
- Floating Call Button with customizable positioning
- Bottom Navigation Bar with active state detection
- Full Elementor integration
- Responsive design for all devices
- Font Awesome icon support
- CSS Variables for easy customization

### Planned Features (v1.1.0)
- WhatsApp Chat Bubble widget
- FAQ Accordion widget
- Testimonial Carousel widget
- Social Media Feed widget
- Advanced animation options
- Performance optimizations

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
