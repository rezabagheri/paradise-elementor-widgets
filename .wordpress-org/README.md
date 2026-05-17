# WordPress.org Marketing Assets

این پوشه شامل asset هایی هست که در صفحه‌ی پلاگین روی [wordpress.org/plugins](https://wordpress.org/plugins/) نمایش داده می‌شن. این فایل‌ها **داخل zip پلاگین قرار نمی‌گیرن** و کاربر دانلودشون نمی‌کنه — فقط روی WP.org استفاده می‌شن.

## ساختار فایل‌ها

| فایل | کاربرد | فرمت پذیرفته‌شده توسط WP.org |
|------|--------|--------------------------------|
| `icon.svg` | آیکون اصلی پلاگین (scalable) | SVG / PNG |
| `icon-256x256.png` | fallback آیکون برای کلاینت‌هایی که SVG رندر نمی‌کنن | PNG ۲۵۶×۲۵۶ دقیقاً |
| `icon-128x128.png` | نمایش در لیست افزونه‌ها (admin) | PNG ۱۲۸×۱۲۸ دقیقاً |
| `banner-772x250.png` | بنر سرصفحه‌ی پلاگین (low-DPI) | **فقط PNG/JPG** — SVG پذیرفته نمی‌شه |
| `banner-1544x500.png` | همان بنر برای رتینا (2x) | **فقط PNG/JPG** |
| `screenshot-1.png`, `screenshot-2.png`, ... | اسکرین‌شات‌های توضیح در `readme.txt` | PNG / JPG |

> توجه: بنرها **حتماً باید PNG یا JPG باشن**. SVG های موجود در این پوشه (`banner-*.svg`) فقط **template** هستن که باید export بشن.

## Export کردن SVG بنر به PNG

دو راه:

**روش ۱ — Inkscape CLI (سریع، در ترمینال):**
```bash
cd .wordpress-org
inkscape banner-772x250.svg  --export-type=png --export-filename=banner-772x250.png  --export-width=772
inkscape banner-1544x500.svg --export-type=png --export-filename=banner-1544x500.png --export-width=1544
inkscape icon.svg            --export-type=png --export-filename=icon-256x256.png    --export-width=256
inkscape icon.svg            --export-type=png --export-filename=icon-128x128.png    --export-width=128
```

**روش ۲ — Figma / مرورگر:** SVG رو در Figma یا یه viewer باز کن و به‌صورت PNG با عرض‌های دقیق export کن.

## انتشار به WP.org SVN

وقتی پلاگین رو روی [wordpress.org/plugins](https://wordpress.org/plugins/) ثبت کنی، یه SVN repository بهت می‌ده با این ساختار:

```
/trunk/      ← کد پلاگین (همتای master/main)
/tags/3.0.1/ ← snapshot هر نسخه
/assets/     ← آیکون و بنر — اینجا محتوای پوشه‌ی .wordpress-org/ کپی می‌شه
```

**نکته‌ی مهم:** WP.org `assets/` در ریشه‌ی SVN (نه داخل `trunk/`) قرار می‌گیره. این چیزی هست که خیلی‌ها اولین بار اشتباه می‌کنن.

اگه از GitHub Action مثل [`10up/action-wordpress-plugin-deploy`](https://github.com/10up/action-wordpress-plugin-deploy) استفاده کنی، خودش پوشه‌ی `.wordpress-org/` رو به `/assets/` در SVN منتقل می‌کنه.

## نسخه‌ی فعلی لوگو

`icon.svg` یه retake از لوگوی swoosh-P اصلی هست با این تغییرات:

- **پلیت پس‌زمینه‌ی تیره** با گوشه‌های گرد (radius=56 روی viewbox 256) — silhouette رو در سایز کوچیک حفظ می‌کنه
- **حذف لایه‌های translucent** — فقط solid fill ها، چون SVG های نیمه‌شفاف روی پس‌زمینه‌های مختلف رفتار غیرقابل‌پیش‌بینی دارن
- **حفظ DNA اصلی**: swoosh قرمز با شیب، یه لایه‌ی تیره برای عمق، فرم کلی P-as-tilted-oval
- **رنگ قرمز ثابت** (#E30613) — همون قرمز اصلی لوگوی توئه

اگه می‌خوای rebrand کاملاً متفاوتی بکنی (مثلاً monogram جدید) این فایل نقطه‌ی شروع نیست — بهتره از صفر تو Figma طراحی بشه.
