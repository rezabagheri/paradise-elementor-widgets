=== Paradise Elementor Widgets ===
Contributors: rezabagheri
Tags: elementor, elementor widgets, bottom navigation, phone link, mobile navigation
Requires at least: 6.1
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced custom Elementor widgets — Phone Link and Bottom Navigation Bar.

== Description ==

Paradise Elementor Widgets adds powerful, mobile-focused widgets to Elementor:

**Phone Link**
A smart phone number widget that normalizes any input format and builds proper `tel:` links. Supports icons, prefix text, multiple display formats (International, Local, Dashes, Dots, Custom Mask), and flexible link scopes.

**Bottom Navigation Bar**
A fully-featured mobile bottom navigation bar with responsive visibility, active state detection, badge support, speed dial, and a JavaScript hook system. Pixel-perfect editor preview inside the Elementor iframe.

= Phone Link Features =
* Accepts any phone number format — normalizes automatically
* Display formats: Raw, International, Local, Dashes, Dots, Custom Mask
* Layout modes: Number Only, Prefix + Number, Icon + Number, Icon + Prefix + Number
* Inline or Stacked direction
* Link scope: Full Widget, Number Only, or No Link
* Dynamic tag support on phone number and prefix
* Country codes: US, UK, DE, IR, UAE, or Custom

= Bottom Navigation Bar Features =
* Items source: Manual repeater or WordPress Menu
* Badge support: Static, WooCommerce cart count, or JS-driven via `EBN.setBadge()`
* Responsive visibility via Elementor native controls
* Center button actions: Link, Speed Dial, or JS Hook
* Active state detection: URL match, Manual index, or Both
* Active indicators: Top Bar, Bottom Bar, Dot, Pill, or Glow
* Bar position: Full Width or Floating Centered
* Entrance animations: Slide Up, Fade, or Both
* JavaScript Public API: `EBN.setBadge(id, count)`
* JS Hook system: `document.addEventListener('ebn:hook:name', fn)`

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
`EBN.setBadge('your-widget-css-id', count);`
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

= 2.1.0 =
* Added: Elementor native responsive visibility for Bottom Navigation Bar
* Added: Pixel-perfect editor preview inside Elementor iframe
* Added: Speed dial auto-opens in editor for visual feedback
* Added: `animEnabled` and `animStyle` animation controls
* Changed: Rebranded to Paradise
* Fixed: Editor clicks disabled on nav items (standard Elementor behavior)

= 2.0.0 =
* Changed: Removed all `!important` declarations from CSS
* Changed: Introduced CSS variables for theming (`--ebn-bar-height`, `--ebn-anim-duration`)
* Fixed: Editor styles scoped to prevent frontend bleed

= 1.0.0 =
* Initial release
* Phone Link widget
* Bottom Navigation Bar widget

== Upgrade Notice ==

= 2.1.0 =
Improved editor preview and responsive visibility controls. Safe to upgrade.
