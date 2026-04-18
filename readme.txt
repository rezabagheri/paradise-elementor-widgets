=== Paradise Elementor Widgets ===
Contributors: rezabagheri
Tags: elementor, elementor widgets, bottom navigation, phone link, google map, social links, business hours, schema, local seo, announcement bar, cookie consent, sticky header, off canvas menu, back to top
Requires at least: 6.1
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.3.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced custom Elementor widgets for mobile UX, contact, local SEO, and business info — all powered by a centralized Site Info store.

== Description ==

Paradise Elementor Widgets adds powerful, mobile-focused widgets to Elementor. All widgets share a centralized **Site Info** data store (phones, emails, addresses, social links, business hours) so you configure your business details once and reuse them everywhere.

**Site Info**
Centralized business data configured under Paradise → Elementor Widgets. Exposes a `[paradise_site_info]` shortcode and Elementor Dynamic Tags. All widgets that display phone numbers, addresses, maps, social links, or business hours read from Site Info automatically.

**Phone Link**
A smart phone number widget that normalizes any input format and builds proper `tel:` or WhatsApp `wa.me` links. Supports icons, prefix text, multiple display formats, and flexible link scopes.

**Phone Button**
A styled CTA button that dials a phone number or opens WhatsApp. Full button customization with hover effects and responsive sizing.

**Floating Call Button**
A fixed-position call button that stays visible while users scroll. Includes position customization, pulse animation, label support, and breakpoint visibility.

**Bottom Navigation Bar**
A fully-featured mobile bottom navigation bar with responsive visibility, active state detection, badge support (including WooCommerce cart), speed dial, and a JavaScript hook system.

**Author Card**
A professional profile card displaying author information: photo, name, title, bio, custom meta fields, social links, and CTA button. Includes Schema.org Person markup for SEO.

**Announcement Bar**
A fixed full-width banner for announcements, promotions, or alerts. Supports icon, message, CTA button, and dismissal with session / days / permanent memory stored in localStorage.

**Cookie Consent Bar**
A GDPR/cookie consent bar with Accept and Decline buttons. Stores user choice in localStorage with configurable expiry. Dispatches `paradise:consent:accepted` and `paradise:consent:declined` events for analytics integration.

**Back to Top**
A fixed-position button that appears after scrolling past a configurable threshold and smoothly returns the user to the top of the page.

**Off-Canvas Menu**
A slide-in side panel with a WordPress menu. Triggered by an inline button or the `Paradise.openOffCanvas()` JavaScript API (useful with Bottom Nav JS Hooks).

**Sticky Header**
Place inside any Elementor section to make it sticky. Applies configurable scroll effects (shadow, background change, height shrink) after passing a scroll threshold.

**Google Map**
Embeds a Google Map via iframe. Source can be a Site Info address (Map URL field) or a manually entered URL. Supports Place and Directions modes, satellite/hybrid/terrain map types, zoom slider, border radius, and box shadow.

**Social Links**
A row or column of social media icon links. Source: Site Info socials or a custom repeater. Supports brand/uniform colors, lift/scale/color-shift hover animations, circle/rounded icon shapes, and icon-only/icon+label/label-only display modes.

**Business Hours**
Displays business hours from Site Info with a live Open Now / Closed badge. Highlights today's row. Supports 12 h and 24 h formats. The badge updates client-side using the site's timezone (independent of the visitor's browser timezone).

**LocalBusiness Schema**
An invisible widget that outputs Schema.org JSON-LD markup using Site Info data (name, phone, address, social sameAs, openingHoursSpecification). Supports 14 Schema.org business type subtypes. Helps Google display rich results (address, phone, hours).

= Phone Link Features =
* Accepts any phone number format — normalizes automatically
* Display formats: Raw, International, Local, Dashes, Dots, Custom Mask
* Layout modes: Number Only, Prefix + Number, Icon + Number, Icon + Prefix + Number
* Inline or Stacked direction
* Link scope: Full Widget, Number Only, or No Link
* Link type: Phone Call (`tel:`) or WhatsApp (`wa.me/`)
* Dynamic tag support on phone number and prefix

= Bottom Navigation Bar Features =
* Items source: Manual repeater or WordPress Menu
* Badge support: Static, WooCommerce cart count, or JS-driven via `Paradise.setBadge()`
* Responsive visibility via Elementor native controls
* Center button actions: Link, Speed Dial, or JS Hook
* Active state detection: URL match, Manual index, or Both
* Active indicators: Top Bar, Bottom Bar, Dot, Pill, or Glow
* Bar position: Full Width or Floating Centered
* Entrance animations: Slide Up, Fade, or Both
* JavaScript Public API: `Paradise.setBadge(id, count)`
* JS Hook system: `document.addEventListener('ebn:hook:name', fn)`

= Google Map Features =
* Mode: Place (single location) or Directions (origin → destination)
* Map types: Road map, Satellite, Hybrid, Terrain
* Source: Site Info address Map URL or manual entry
* Zoom slider control
* Height, border radius, and box shadow controls
* Travel modes for Directions: Driving, Walking, Bicycling, Transit

= Social Links Features =
* Source: Site Info socials or custom repeater
* 10 built-in platform icons: Instagram, Facebook, Twitter/X, LinkedIn, YouTube, TikTok, Pinterest, Snapchat, Threads, WhatsApp
* Color modes: Brand colors or Uniform color
* Hover animations: None, Lift, Scale, Color Shift
* Icon shapes: None, Circle, Rounded Square
* Display modes: Icon only, Icon + Label, Label only
* Layout: Horizontal row or Vertical column

== Installation ==

1. Make sure Elementor (free) is installed and activated.
2. Upload the `paradise-elementor-widgets` folder to `/wp-content/plugins/`.
3. Activate the plugin from **Plugins** in the WordPress admin.
4. Configure business details under **Paradise → Elementor Widgets → Site Info**.
5. Open Elementor editor — the **Paradise Widgets** category will appear in the widget panel.

== Frequently Asked Questions ==

= Does this require Elementor Pro? =
No. The free version of Elementor is sufficient. Elementor Pro is optional and unlocks Theme Builder support and Dynamic Tags on Pro controls.

= Is this compatible with the latest version of Elementor? =
Yes. The plugin is tested with Elementor 3.5 and above.

= What is Site Info? =
Site Info is a centralized store for your business data (phones, emails, addresses, social links, business hours). Configure it once and all widgets that need this data read from it automatically. It also provides a shortcode and Elementor Dynamic Tags.

= How do I embed a Google Map? =
Go to Google Maps, search for your location, click Share → Embed a map → copy the `src` URL from the iframe code. Paste it into the Site Info address "Map URL" field, or directly in the Google Map widget. The plugin normalizes most Google Maps URL formats automatically.

= How do I set a badge count from JavaScript? =
Use the public API: `Paradise.setBadge('your-widget-css-id', count);`
Setting count to 0 hides the badge. Count above 99 displays as "99+".

= How do I trigger the Off-Canvas Menu from Bottom Navigation? =
Set a Bottom Nav center button (or item) action to **JS Hook** and enter a hook name (e.g. `openMenu`). In the Off-Canvas Menu widget, set the JS trigger to the same hook name. The menu will open when the button is tapped.

= Does the Bottom Navigation Bar work with WooCommerce? =
Yes. Set the badge type to **WooCommerce Cart** and it will display the live cart item count automatically.

= How accurate is the Business Hours "Open Now" badge? =
The badge is computed in the visitor's browser using the site's timezone (from WordPress Settings → General). It does not depend on the visitor's device timezone, so it always reflects your business's local time.

== Screenshots ==

1. Site Info admin page — phones, addresses, social links, and business hours
2. Google Map widget — Place mode with satellite type
3. Social Links widget — brand color icon row
4. Business Hours widget with live Open Now badge
5. Bottom Navigation Bar — frontend on mobile
6. Announcement Bar with dismiss button
7. Widget panel — Paradise Widgets category

== Changelog ==

= 2.3.0 =
* Added: Site Info centralized data store (phones, emails, addresses, socials, business hours) with shortcode and Elementor Dynamic Tags
* Added: Business Hours widget — live Open Now / Closed badge, today highlight, 12 h / 24 h format
* Added: LocalBusiness Schema widget — Schema.org JSON-LD with 14 business type subtypes
* Added: Google Map widget — Place and Directions modes, satellite/hybrid/terrain, zoom, border radius, shadow
* Added: Social Links widget — Site Info or custom source, brand colors, hover animations, shapes
* Added: Announcement Bar widget — icon, message, CTA, dismiss with session/days/permanent memory
* Added: Cookie Consent Bar widget — Accept/Decline, localStorage expiry, analytics events
* Added: Back to Top widget — scroll threshold, smooth scroll
* Added: Off-Canvas Menu widget — WordPress menu in slide-in panel, JS API trigger
* Added: Sticky Header widget — scroll effects (shadow, background, shrink)
* Added: Site Info map_url field on address entries
* Added: Drag-to-reorder for all Site Info repeater sections
* Added: label attribute on [paradise_site_info] shortcode
* Changed: register_widgets() now driven by a registry array — one entry per widget
* Fixed: Google Map /maps/dir/ URLs refused to connect in iframe — now rewritten correctly
* Fixed: Google Map zoom ignored with /maps/embed?q= format — now uses reliable URL format

= 2.2.0 =
* BREAKING: Bottom Navigation widget name changed from ebn_bottom_nav to paradise_bottom_nav — re-save existing widgets
* BREAKING: Bottom Nav CSS class prefix updated to paradise-bn-
* Added: WhatsApp link support in Phone Link widget
* Added: WooCommerce cart count badge in Bottom Navigation Bar
* Added: Schema.org markup to Author Card
* Added: JS Hook system for center button custom actions
* Added: Custom CSS mask for phone number display
* Fixed: Bottom Nav items no longer interfere with speed dial
* Fixed: Phone Link properly escapes all output
* Improved: Pixel-perfect editor preview inside Elementor iframe

= 2.1.0 =
* Added: Elementor native responsive visibility for Bottom Navigation Bar
* Added: Pixel-perfect editor preview inside Elementor iframe
* Added: Speed dial auto-opens in editor for visual feedback
* Changed: Rebranded to Paradise

= 2.0.0 =
* Changed: Removed all !important declarations from CSS
* Changed: Introduced CSS variables for theming

= 1.0.0 =
* Initial release
* Phone Link widget
* Bottom Navigation Bar widget

== Upgrade Notice ==

= 2.3.0 =
New: Site Info, Business Hours, LocalBusiness Schema, Google Map, Social Links, Announcement Bar, Cookie Consent Bar, Back to Top, Off-Canvas Menu, Sticky Header. Safe to upgrade — no breaking changes from 2.2.0.

= 2.2.0 =
Important: Bottom Navigation widgets require manual re-save in Elementor after this update due to name change.
