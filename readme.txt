=== Paradise Elementor Widgets ===
Contributors: rezabagheri
Tags: elementor, elementor widgets, bottom navigation, phone link, phone button, floating call button, author card, mobile navigation
Requires at least: 6.1
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.2.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced custom Elementor widgets — Phone Link and Bottom Navigation Bar.

== Description ==

Paradise Elementor Widgets adds powerful, mobile-focused widgets to Elementor:

**Author Card**
A professional profile card displaying author information: photo, name, title, bio, custom meta fields, social links, and CTA button. Includes Schema.org Person markup for SEO.

**Phone Link**
A smart phone number widget that normalizes any input format and builds proper `tel:` links. Supports icons, prefix text, multiple display formats (International, Local, Dashes, Dots, Custom Mask), and flexible link scopes.

**Phone Button**
A styled CTA button that dials a phone number or opens WhatsApp. Full button customization with hover effects and responsive sizing.

**Floating Call Button**
A fixed-position call button that stays visible while users scroll. Includes position customization, pulse animation, label support, and breakpoint visibility.

**Bottom Navigation Bar**
A fully-featured mobile bottom navigation bar with responsive visibility, active state detection, badge support, speed dial, and a JavaScript hook system. Pixel-perfect editor preview inside the Elementor iframe.

= Phone Link Features =
* Accepts any phone number format — normalizes automatically
* Display formats: Raw, International, Local, Dashes, Dots, Custom Mask
* Layout modes: Number Only, Prefix + Number, Icon + Number, Icon + Prefix + Number
* Inline or Stacked direction
* Link scope: Full Widget, Number Only, or No Link
* Link type: Phone Call (`tel:`) or WhatsApp (`wa.me/`)
* Dynamic tag support on phone number and prefix
* Country codes: US, UK, DE, IR, UAE, or Custom

= Phone Button Features =
* Auto-formatted phone number
* Button text with dynamic tag support
* Display formats: Raw, International, Local, Dashes, Dots, Custom Mask
* Link type: Phone Call or WhatsApp
* Icon positioning and color
* Full button styling: background, border, shadow, hover effects
* Responsive sizing and padding

= Floating Call Button Features =
* Phone number with all formatting options
* Optional label text
* Fixed-position button with corner customization
* Pulse animation (on/off with speed control)
* Position offset controls
* Full button styling with hover and animation effects
* Responsive visibility per breakpoint
* Link type: Phone Call or WhatsApp

= Author Card Features =
* Photo source: Paradise meta, Gravatar, custom meta key, or static upload
* Name, title/credentials, and bio with optional links
* Custom fields repeater: Text, Link, Email, or Badge type
* Social links with icon-only or icon+label display
* CTA button with full styling and link options
* Layout: Vertical or Horizontal
* Alignment control cascading to all sections
* Schema.org Person markup for SEO

= Bottom Navigation Bar Features =
* Items source: Manual repeater or WordPress Menu
* Badge support: Static, WooCommerce cart count, or JS-driven via `Paradise.setBadge()`
* Responsive visibility via Elementor native controls
* Center button actions: Link, Speed Dial, or JS Hook
* Active state detection: URL match, Manual index, or Both
* Active indicators: Top Bar, Bottom Bar, Dot, Pill, or Glow
* Bar position: Full Width or Floating Centered
* Entrance animations: Slide Up, Fade, or Both
* Pixel-perfect editor preview inside Elementor iframe
* JavaScript Public API: `Paradise.setBadge(id, count)`
* JS Hook system: `document.addEventListener('ebn:hook:name', fn)`

= Phone Link v2.2.0 Additions =
* WhatsApp integration: `https://wa.me/{digits}` links with auto-prefixing
* Custom phone number masks for flexible display formats
* Improved escaping and sanitization for security

== Installation ==

1. Make sure Elementor (free) is installed and activated.
2. Upload the `paradise-elementor-widgets` folder to `/wp-content/plugins/`.
3. Activate the plugin from **Plugins** in the WordPress admin.
4. Open Elementor editor — the **Paradise Widgets** category will appear in the widget panel.

== Frequently Asked Questions ==

= Does this require Elementor Pro? =
No. The free version of Elementor is sufficient. Elementor Pro is optional and unlocks Theme Builder support.

= Is this compatible with the latest version of Elementor? =
Yes. The plugin is tested with Elementor 3.5 and above.

= How do I set a badge count from JavaScript? =
Use the public API:
`Paradise.setBadge('your-widget-css-id', count);`
Setting count to 0 hides the badge. Count above 99 displays as "99+".

= How do I trigger custom behavior from the center button? =
Set the center button action to **JS Hook**, enter a hook name, then listen in JavaScript:
`document.addEventListener('ebn:hook:yourHookName', function(e) { ... });`

= Does the Bottom Navigation Bar work with WooCommerce? =
Yes. Set the badge type to **WooCommerce Cart** and it will display the live cart item count automatically.

== Screenshots ==

1. Bottom Navigation Bar — frontend on mobile
2. Bottom Navigation Bar — Elementor editor preview
3. Phone Link widget — layout options
4. Widget panel — Paradise Widgets category

== Changelog ==

= 2.2.0 =
* **BREAKING**: Bottom Navigation widget `get_name()` changed from `ebn_bottom_nav` to `paradise_bottom_nav` — existing widgets will need manual re-save in Elementor
* **BREAKING**: CSS class prefix for Bottom Nav updated to `paradise-bn-` for consistency with other widgets
* Added: WhatsApp link support in Phone Link widget (automatic +country code prefixing)
* Added: WooCommerce cart count badge in Bottom Navigation Bar
* Added: Schema.org markup to Author Card (improves SEO)
* Added: JS Hook system for center button custom actions
* Added: Custom CSS mask for phone number display (e.g., `(###) ###-####`)
* Fixed: Bottom Nav items no longer interfere with speed dial interactions
* Fixed: Phone Link properly escapes all output for security
* Fixed: Bottom Nav alignment cascade in Elementor editor
* Improved: Editor preview now uses `position: fixed` for pixel-perfect alignment inside iframe
* Improved: Responsive visibility respects Elementor's native breakpoints
* Improved: Speed Dial visible by default in editor for real-time feedback
* Improved: CSS class prefixes now widget-specific for better maintainability

= 2.1.0 =
* Added: Elementor native responsive visibility for Bottom Navigation Bar
* Added: Pixel-perfect editor preview inside Elementor iframe
* Added: Speed dial auto-opens in editor for visual feedback
* Added: `animEnabled` and `animStyle` animation controls
* Changed: Rebranded to Paradise
* Fixed: Editor clicks disabled on nav items (standard Elementor behavior)

= 2.0.0 =
* Changed: Removed all `!important` declarations from CSS
* Changed: Introduced CSS variables for theming (`--paradise-bar-height`, `--paradise-anim-duration`)
* Fixed: Editor styles scoped to prevent frontend bleed

= 1.0.0 =
* Initial release
* Phone Link widget
* Bottom Navigation Bar widget

== Upgrade Notice ==

= 2.2.0 =
**Important:** Bottom Navigation widgets require manual re-save in Elementor after this update due to name change. Not a breaking change for display — just re-save each Bottom Navigation Bar widget once. All other widgets auto-upgrade.

= 2.1.0 =
Improved editor preview and responsive visibility controls. Safe to upgrade.
