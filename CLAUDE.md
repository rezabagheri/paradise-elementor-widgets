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
paradise-elementor-widgets/
├── paradise-elementor-widgets.php        # Main plugin file
├── CLAUDE.md                             # این فایل
├── admin/
│   ├── class-paradise-ew-admin.php       # Settings registration, menus, toggles
│   ├── class-paradise-user-profile.php   # User profile social/credentials fields
│   └── views/
│       └── page-settings.php             # Admin settings page HTML
├── assets/
│   ├── css/
│   │   ├── admin.css                     # Admin settings page styles
│   │   ├── author-card.css               # Author Card widget styles
│   │   ├── phone-link.css                # Phone Link widget styles
│   │   ├── phone-button.css              # Phone Button widget styles
│   │   ├── floating-call-btn.css         # Floating Call Button widget styles
│   │   ├── announcement-bar.css          # Announcement Bar widget styles
│   │   └── bottom-nav.css                # Bottom Nav widget styles
│   └── js/
│       ├── bottom-nav.js                 # Bottom Nav widget JS
│       └── announcement-bar.js           # Announcement Bar dismiss logic
├── includes/
│   └── trait-paradise-phone-helper.php   # Shared phone normalization trait
└── widgets/
    ├── class-paradise-author-card.php
    ├── class-paradise-phone-link.php
    ├── class-paradise-phone-button.php
    ├── class-paradise-floating-call-btn.php
    ├── class-paradise-announcement-bar.php
    └── class-paradise-bottom-nav.php
```

---

## نسخه فعلی

**Plugin version: 2.2.0**
```php
define( 'PARADISE_EW_VERSION', '2.2.0' );
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
- بدون `!important`
- CSS class prefix هر ویجت باید widget-specific باشد (نه فقط `paradise-`)
  - Author Card: `paradise-author-card__*`
  - Phone Link: `paradise-phone-*`
  - Bottom Nav: `paradise-bn-*`
- CSS variables برای theming: `--paradise-bn-bar-height`, `--paradise-bn-anim-duration`, `--paradise-bn-editor-bottom`
- Editor styles فقط داخل `body.elementor-editor-active` scope می‌شوند
- Handle names:
  - `paradise-author-card-style` — Author Card CSS
  - `paradise-phone-link` — Phone Link CSS
  - `paradise-bn-bottom-nav-style` — Bottom Nav CSS
  - `paradise-bn-bottom-nav-script` — Bottom Nav JS

### JavaScript
- Vanilla JS، بدون jQuery dependency
- IIFE pattern: `(function() { 'use strict'; ... })()`
- Public API روی `window.Paradise`
- Custom events: `ebn:hook:{name}` روی document
- هیچ localStorage یا sessionStorage استفاده نمی‌شود

### Elementor Controls
- Visibility: از `add_responsive_control` استفاده کن، نه custom breakpoint
- Dynamic tags: `'dynamic' => [ 'active' => true ]` روی text controls
- Editor preview: `\Elementor\Plugin::$instance->editor->is_edit_mode()`
- Alignment cascade: به جای control جداگانه برای هر section، از CSS class-based cascade استفاده کن
  - مثال: `.paradise-author-card--align-center .paradise-author-card__social { justify-content: center }`

---

## ویجت ۱ — Paradise Author Card

**فایل:** `widgets/class-paradise-author-card.php`
**CSS:** `assets/css/author-card.css`
**Class:** `Paradise_Author_Card_Widget`
**get_name():** `paradise_author_card`
**Category:** `paradise`

### قابلیت‌ها
- Photo با link قابل انتخاب (photo page, author archive, custom URL, none)
- Name با link قابل انتخاب
- Title/Credentials — inline (کنار name) یا جداگانه
- Bio (description)
- Custom Fields (repeater): انواع text / link / email / badge
  - field_type: `text` → `<span>`, `link` → `<a href>`, `email` → `<a href="mailto:">`, `badge` → `<span class="...__field-badge">`
  - field_show_label: yes/no — نمایش label با colon
- Social Links (repeater): icon + label + href
  - Label mode: icon-only / icon + label
- CTA Button با href قابل انتخاب
- Layout: Vertical / Horizontal
- Alignment: Left / Center / Right — کنترل یک‌جا همه section‌ها را align می‌کند
- Schema.org Person markup (itemprop) روی همه عناصر اصلی

### HTML classes
```
.paradise-author-card                               (wrapper — itemscope itemtype="https://schema.org/Person")
.paradise-author-card--vertical | --horizontal
.paradise-author-card--align-left | --align-center | --align-right
.paradise-author-card__photo-wrap
.paradise-author-card__photo-link
.paradise-author-card__photo                        (itemprop="image")
.paradise-author-card__body
.paradise-author-card__name                         (itemprop="name")
.paradise-author-card__name-link
.paradise-author-card__title                        (itemprop="jobTitle")
.paradise-author-card__title--inline                (کنار name)
.paradise-author-card__bio                          (itemprop="description")
.paradise-author-card__fields
.paradise-author-card__field
.paradise-author-card__field-label
.paradise-author-card__field-value
.paradise-author-card__field-link                   (برای type=link و type=email)
.paradise-author-card__field-badge                  (برای type=badge)
.paradise-author-card__cta
.paradise-author-card__btn                          (itemprop="url")
.paradise-author-card__social
.paradise-author-card__social-link                  (itemprop="sameAs" یا "email")
.paradise-author-card__social-icon
.paradise-author-card__social-label
.paradise-author-card__placeholder                  (فقط در editor بدون محتوا)
```

### Alignment cascade (CSS)
```css
/* Alignment control فقط یک class روی wrapper اضافه می‌کند */
.paradise-author-card--align-center .paradise-author-card__social  { justify-content: center; }
.paradise-author-card--align-right  .paradise-author-card__social  { justify-content: flex-end; }
.paradise-author-card--align-center .paradise-author-card__fields  { align-items: center; }
.paradise-author-card--align-right  .paradise-author-card__fields  { align-items: flex-end; }
```

---

## ویجت ۲ — Paradise Phone Link

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
- Country code: US, UK, DE, IR, UAE, Custom — همیشه visible (مستقل از display_format)
- Link type: Phone Call (`tel:`) / WhatsApp (`https://wa.me/{digits}`)
  - WhatsApp: اگر شماره با `+` شروع نشود، country code prefix می‌شود
  - WhatsApp: `target="_blank" rel="noopener noreferrer"` به لینک اضافه می‌شود
- Link scope: Full Widget / Number Only / No Link
- `aria-label` روی لینک: "Call {number}" یا "WhatsApp {number}"
- Icon با hover color control

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

## ویجت ۳ — Bottom Navigation Bar

**فایل:** `widgets/class-paradise-bottom-nav.php`
**CSS:** `assets/css/bottom-nav.css` (handle: `paradise-bn-bottom-nav-style`)
**JS:** `assets/js/bottom-nav.js` (handle: `paradise-bn-bottom-nav-script`)
**Class:** `Paradise_Bottom_Nav_Widget`
**get_name():** `paradise_bottom_nav`
**Category:** `paradise`

### قابلیت‌ها
- Items source: Manual (Repeater) / WordPress Menu
- Badge: Static / WooCommerce Cart / JS-driven (`Paradise.setBadge(id, count)`)
- Visibility: `add_responsive_control('bar_display')` — Elementor native responsive
  - Desktop default: hidden (`none`), Tablet+Mobile default: visible (`block`)
  - JS mirrors same logic: `showOnMobile`, `showOnTablet`, `showOnDesktop` در data config
- Center button actions: Link / Speed Dial / JS Hook (`ebn:hook:{name}`)
- Active detection: URL Match / Manual / Both
- URL match mode: Pathname Only / Full URL
- Active indicator: None / Top Bar / Bottom Bar / Dot / Pill / Glow
- Bar position: Full Width / Floating Centered
- Entrance animation: Slide Up / Fade / Both — قابل disable
- Editor preview: pixel-perfect، speed dial باز، لینک‌ها غیرفعال

### HTML classes
```
.paradise-bn-wrapper.paradise-bn-pos-full | .paradise-bn-pos-floating
.paradise-bn-wrapper.paradise-bn-is-editor  (فقط در editor)
.paradise-bn-bar
.paradise-bn-indicator.paradise-bn-indicator--top_bar | --bot_bar
.paradise-bn-item.paradise-bn-item--active.paradise-bn-pill
.paradise-bn-item-icon
.paradise-bn-badge
.paradise-bn-dot
.paradise-bn-label
.paradise-bn-center-wrap
.paradise-bn-center-btn
.paradise-bn-center-icon
.paradise-bn-center-label
.paradise-bn-speed-dial.paradise-bn-speed-dial--open
.paradise-bn-dial-item
.paradise-bn-dial-icon
.paradise-bn-dial-label
.paradise-bn-overlay.paradise-bn-overlay--active
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
  "editorDialOpen": false,
  "showOnMobile": true,
  "showOnTablet": true,
  "showOnDesktop": false
}
```

### JS Public API
```javascript
Paradise.setBadge('css-id', count);  // set badge
document.addEventListener('ebn:hook:myHook', fn);  // JS Hook
```

### CSS variables
```css
--paradise-bn-bar-height      /* تنظیم شده توسط JS */
--paradise-bn-anim-duration   /* تنظیم شده توسط JS */
--paradise-bn-editor-bottom   /* فقط در editor */
```

### Responsive visibility — معماری مهم
CSS `display:none` پیش‌فرض + JS `applyResponsiveVisibility()` مدیریت visibility را در دست می‌گیرد.
**هرگز** از `wrapper.style.display = 'block'` بدون بررسی breakpoint استفاده نکن — inline style هر CSS media query را override می‌کند.
Breakpoints در JS: mobile ≤ 767px، tablet 768–1024px، desktop > 1024px.

---

## ویجت ۶ — Announcement Bar

**فایل:** `widgets/class-paradise-announcement-bar.php`
**CSS:** `assets/css/announcement-bar.css` (handle: `paradise-announcement-bar`)
**JS:** `assets/js/announcement-bar.js` (handle: `paradise-announcement-bar`)
**Class:** `Paradise_Announcement_Bar_Widget`
**get_name():** `paradise_announcement_bar`
**Category:** `paradise`

### قابلیت‌ها

- Icon (Elementor Icons_Manager)
- Message (TEXTAREA با dynamic tag)
- CTA Button: text + URL (فقط نمایش داده می‌شود اگر هر دو پر باشند)
- Close Button با dismiss memory:
  - `session` — sessionStorage
  - `days` — localStorage با JSON `{ expires: timestamp }`
  - `forever` — localStorage با string `'forever'`
- Unique Bar ID: پیش‌فرض از Elementor widget ID — قابل override برای اشتراک state بین صفحات
- Bar Position: Top / Bottom با `prefix_class='paradise-ab-pos-'`

### HTML structure

```html
<div class="paradise-ab-wrap" data-ab-id="..." data-ab-duration="..." data-ab-days="..." [data-ab-edit="true"]>
    <div class="paradise-ab-inner">
        <span class="paradise-ab-icon">...</span>           <!-- optional -->
        <span class="paradise-ab-message">...</span>
        <a class="paradise-ab-cta" href="...">...</a>       <!-- optional -->
        <button class="paradise-ab-close" aria-label="Close announcement">
            <svg><!-- inline X --></svg>
        </button>
    </div>
</div>
```

### HTML classes

```text
.paradise-ab-wrap[data-ab-hidden="true"]   (JS هایدمی‌کند وقتی dismiss شود)
.paradise-ab-inner
.paradise-ab-icon
.paradise-ab-message
.paradise-ab-cta
.paradise-ab-close
```

### JS dismiss logic

```javascript
// Storage key: 'paradise-ab-' + id
// session:  sessionStorage.setItem(key, '1')
// days:     localStorage.setItem(key, JSON.stringify({ expires: Date.now() + days * 86400000 }))
// forever:  localStorage.setItem(key, 'forever')
```

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
5. **PHP class name** تغییر کرد (EBN_Widget → Glenar_Bottom_Nav_Widget → Paradise_Bottom_Nav_Widget)
6. **Speed dial** در editor همیشه باز است
7. **Badge WooCommerce**: از `WC()->cart->get_cart_contents_count()` استفاده می‌کند
8. **CSS class prefix** widget-specific شد (v2.2): `paradise-bn-` برای bottom nav، نه `paradise-` generic
9. **get_name() bottom nav**: از `ebn_bottom_nav` به `paradise_bottom_nav` تغییر کرد (v2.2)
10. **Schema.org**: itemprop attributes روی Author Card — zero visible change، فقط SEO
11. **Alignment در Author Card**: یک "Alignment" control در Layout section — cascade به social و fields از طریق CSS class
12. **WhatsApp link**: `https://wa.me/{digits}` — همان digit normalization مثل `tel:`
13. **Responsive visibility در Bottom Nav**: JS باید breakpoint را چک کند، نه CSS — چون inline style همه media query را override می‌کند

---

## Roadmap (بحث شده، تصمیم نهایی نگرفته)

### زیرساخت
- [ ] Settings Page در WordPress Admin
- [ ] Shared Design Tokens (رنگ و فونت مشترک بین ویجت‌ها)
- [ ] Update Mechanism (GitHub Updater یا custom endpoint)

### ویجت‌های احتمالی
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
