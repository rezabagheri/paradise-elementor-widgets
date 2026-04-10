# Paradise Elementor Widgets — Project Context

> این فایل context کامل پروژه است.
> در هر session جدید، Claude این فایل را می‌خواند و بدون توضیح اضافه کار را ادامه می‌دهد.

---

## پروژه چیست

یک پلاگین WordPress که ویجت‌های سفارشی برای Elementor ارائه می‌دهد.
برند: **Paradise** — سایت: paradisecyber.com
نویسنده: Reza Bagheri — rezabagheri@gmail.com

**زبان ارتباطی:** فارسی (کد و placeholder و label همه انگلیسی)
**کشور پیش‌فرض:** آمریکا (US +1، فرمت شماره تلفن آمریکایی)

---

## ساختار فایل‌ها

```
parsdise-elementor-widgets/
├── paradise-elementor-widgets.php   # Main plugin file
├── CLAUDE.md                        # این فایل
├── assets/
│   ├── css/
│   │   ├── phone-link.css           # Phone Link widget styles
│   │   └── bottom-nav.css           # Bottom Nav widget styles (handle: ebn-style)
│   └── js/
│       └── bottom-nav.js            # Bottom Nav widget JS (handle: ebn-script)
└── widgets/
    ├── class-paradise-phone-link.php
    └── class-paradise-bottom-nav.php
```

---

## نسخه فعلی

**Plugin version: 2.1.0**
```php
define( 'PARADISE_EW_VERSION', '2.1.0' );
```

---

## Conventions کد

### PHP
- PHP 7.4+ — از arrow functions و typed returns استفاده می‌کنیم
- هر ویجت یک فایل مستقل در `widgets/`
- نام class: `Paradise_{Name}_Widget extends Widget_Base`
- Register در `paradise-elementor-widgets.php` داخل `register_widgets()`
- از `PARADISE_EW_DIR` و `PARADISE_EW_URL` constants استفاده کن (نه `__DIR__` مستقیم)
- `$_SERVER` values همیشه sanitize می‌شوند
- `esc_html`, `esc_url`, `esc_attr` همیشه در render

### CSS
- بدون `!important` (حذف شد در v2.0)
- CSS variables برای theming: `--ebn-bar-height`, `--ebn-anim-duration`, `--ebn-editor-bottom`
- Editor styles فقط داخل `body.elementor-editor-active` scope می‌شوند
- Handle names که باید ثابت بمانند (backward compat):
  - `ebn-style` — Bottom Nav CSS
  - `ebn-script` — Bottom Nav JS
  - `paradise-phone-link` — Phone Link CSS

### JavaScript
- Vanilla JS، بدون jQuery dependency
- IIFE pattern: `(function() { 'use strict'; ... })()`
- Public API روی `window.EBN`
- Custom events: `ebn:hook:{name}` روی document
- هیچ localStorage یا sessionStorage استفاده نمی‌شود

### Elementor Controls
- Visibility: از `add_responsive_control` استفاده کن، نه custom breakpoint
- Dynamic tags: `'dynamic' => [ 'active' => true ]` روی text controls
- Editor preview: `\Elementor\Plugin::$instance->editor->is_edit_mode()`

---

## ویجت ۱ — Paradise Phone Link

**فایل:** `widgets/class-paradise-phone-link.php`
**CSS:** `assets/css/phone-link.css`
**Class:** `Paradise_Phone_Link_Widget`
**get_name():** `paradise_phone_link`
**Category:** `paradise`

### قابلیت‌ها
- Phone number با dynamic tag support
- Prefix با HTML tag قابل انتخاب (h1-h6, p, div, span) و dynamic tag
- Layout: Number Only / Prefix+Number / Icon+Number / Icon+Prefix+Number
- Direction: Inline / Stacked
- Phone format: Raw / International / Local / Dashes / Dots / Custom Mask
- Country code: US, UK, DE, IR, UAE, Custom
- Link scope: Full Widget / Number Only / No Link
- Normalize phone: همه فرمت‌های ورودی → clean digits → tel: href

### HTML classes
```
.paradise-phone-link-wrapper
.paradise-phone-inner.paradise-inline | .paradise-stacked
.paradise-phone-prefix
.paradise-phone-number
.paradise-phone-icon
.paradise-phone-number-link  (اگر link scope = number only)
```

---

## ویجت ۲ — Bottom Navigation Bar

**فایل:** `widgets/class-paradise-bottom-nav.php`
**CSS:** `assets/css/bottom-nav.css` (handle: `ebn-style`)
**JS:** `assets/js/bottom-nav.js` (handle: `ebn-script`)
**Class:** `Paradise_Bottom_Nav_Widget`
**get_name():** `ebn_bottom_nav` ← مهم: برای backward compat تغییر نمی‌کند
**Category:** `paradise`

### قابلیت‌ها
- Items source: Manual (Repeater) / WordPress Menu
- Badge: Static / WooCommerce Cart / JS-driven (`EBN.setBadge(id, count)`)
- Visibility: `add_responsive_control('bar_display')` — Elementor native responsive
- Center button actions: Link / Speed Dial / JS Hook (`ebn:hook:{name}`)
- Active detection: URL Match / Manual / Both
- URL match mode: Pathname Only / Full URL
- Active indicator: None / Top Bar / Bottom Bar / Dot / Pill / Glow
- Bar position: Full Width / Floating Centered
- Entrance animation: Slide Up / Fade / Both — قابل disable
- Editor preview: pixel-perfect، speed dial باز، لینک‌ها غیرفعال

### HTML classes
```
.ebn-wrapper.ebn-pos-full | .ebn-pos-floating
.ebn-wrapper.ebn-is-editor  (فقط در editor)
.ebn-bar
.ebn-indicator.ebn-indicator--top_bar | --bot_bar
.ebn-item.ebn-item--active.ebn-pill
.ebn-item-icon
.ebn-badge
.ebn-dot
.ebn-label
.ebn-center-wrap
.ebn-center-btn
.ebn-center-icon
.ebn-center-label
.ebn-speed-dial.ebn-speed-dial--open
.ebn-dial-item
.ebn-dial-icon
.ebn-dial-label
.ebn-overlay.ebn-overlay--active
```

### JS data attribute
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

### JS Public API
```javascript
EBN.setBadge('css-id', count);  // set badge
document.addEventListener('ebn:hook:myHook', fn);  // JS Hook
```

### CSS variables
```css
--ebn-bar-height      /* تنظیم شده توسط JS */
--ebn-anim-duration   /* تنظیم شده توسط JS */
--ebn-editor-bottom   /* فقط در editor */
```

### Backward Compatibility (مهم — دست نزن)
| آیتم | مقدار |
|------|-------|
| get_name() | `ebn_bottom_nav` |
| CSS handle | `ebn-style` |
| JS handle | `ebn-script` |
| همه control IDs | حفظ شده از v1.x |

---

## Main Plugin File

```php
// paradise-elementor-widgets.php
// Hook: elementor/init (نه plugins_loaded یا init)
// Assets: wp_register_style/script در elementor/frontend/after_enqueue_styles
// Category: 'paradise'
```

### اضافه کردن ویجت جدید
```php
// 1. فایل بساز: widgets/class-paradise-{name}.php
// 2. در register_widgets() اضافه کن:
require_once PARADISE_EW_DIR . 'widgets/class-paradise-{name}.php';
$widgets_manager->register( new Paradise_{Name}_Widget() );
// 3. اگر CSS/JS دارد: در enqueue_assets() register کن
```

---

## Requirements

| آیتم | نسخه |
|------|-------|
| PHP | 7.4+ |
| WordPress | 6.1+ |
| Elementor | 3.5+ |
| Elementor Pro | اختیاری (برای Theme Builder) |

---

## تصمیم‌های طراحی گذشته

1. **`elementor/init`** به جای `plugins_loaded` — بارگذاری درست
2. **`add_responsive_control`** برای visibility — نه custom breakpoint
3. **`position: fixed`** در editor حفظ شد (نسبت به iframe viewport)
4. **`!important`** از همه CSS حذف شد (v2.0)
5. **PHP class name** تغییر کرد (EBN_Widget → Glenar_Bottom_Nav_Widget → Paradise_Bottom_Nav_Widget) اما `get_name()` ثابت ماند
6. **Speed dial** در editor همیشه باز است
7. **Badge WooCommerce**: از `WC()->cart->get_cart_contents_count()` استفاده می‌کند

---

## Roadmap (بحث شده، تصمیم نهایی نگرفته)

### زیرساخت
- [ ] Settings Page در WordPress Admin
- [ ] Shared Design Tokens (رنگ و فونت مشترک بین ویجت‌ها)
- [ ] Update Mechanism (GitHub Updater یا custom endpoint)

### ویجت‌های احتمالی
- [ ] Click to Call / WhatsApp Button (مکمل Phone Link)
- [ ] Off-Canvas Menu (trigger از Bottom Nav JS Hook)
- [ ] Sticky Header
- [ ] Cookie Consent Bar
- [ ] Announcement Bar

### توزیع
- [ ] تصمیم نهایی: داخلی / مشتریان / WordPress.org

---

## Git Workflow (پیشنهادی)

```bash
main          ← stable releases
develop       ← integration branch
feature/      ← هر ویجت یا feature جدید
```

```bash
# ویجت جدید
git checkout -b feature/paradise-cta-button
# کار...
git commit -m "feat: add CTA Button widget"
git merge develop
```

---

## لینک‌ها

- Plugin URI: https://www.paradisecyber.com/elementor-widgets
- Author: Reza Bagheri — rezabagheri@gmail.com
- Author URI: https://www.paradisecyber.com
- Text Domain: `paradise-elementor-widgets`
