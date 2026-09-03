# Separate theme fixes

Four issues found while reading `header.php` and `functions.php`. None are
caused by the shared sections work and none block it — deploy them separately
so that if something breaks you know which change did it.

Ordered by how much they matter.

---

## 1. `header.php` line 190 — undefined variable

```php
<div class="single-featured-image-header <?php echo $outer_class; ?>">
```

`$outer_class` is never assigned anywhere in the file. On PHP 8 that is a
warning on every front-page load where the page has a featured image, and with
`WP_DEBUG_DISPLAY` on it prints into the markup.

**Fix** — it was never doing anything, so drop it:

```php
<div class="single-featured-image-header">
```

---

## 2. `functions.php` lines 369–375 — shortcodes run on every ACF field

```php
add_filter('acf/format_value', 'run_shortcodes_in_acf_fields', 10, 3);
function run_shortcodes_in_acf_fields($value, $post_id, $field) {
    if (is_string($value)) {
        return do_shortcode($value);
    }
    return $value;
}
```

This runs `do_shortcode()` over **every string value of every ACF field on
every request** — headings, phone numbers, URLs, the lot. Three costs:

- Needless parsing on fields that will never contain a shortcode. It is about
  to start scanning 1,400-word blocks on 29 pages.
- Any text containing square brackets gets mangled.
- A shortcode saved into a field renders wherever that field is output, which
  is a privilege-escalation shape if lower-privileged users can edit fields.

**Fix** — scope it to the fields that actually need it:

```php
// Only the banner fields are allowed to contain shortcodes.
foreach ( array( 'banner_content', 'banner_heading' ) as $vv_field ) {
    add_filter( "acf/format_value/name={$vv_field}", 'run_shortcodes_in_acf_fields', 10, 3 );
}
```

Add any other field names you know rely on it. If you are not sure which do,
grep the database for `[` inside `wp_postmeta` before narrowing.

---

## 3. `header.php` lines 15–38 — Google Analytics loads twice

`gtag.js?id=G-T2S7QD9EQ3` is included at lines 15–22 and again at 30–38, each
followed by its own `gtag('config', 'G-T2S7QD9EQ3')`. GTM (`GTM-KXB9NSQP`) is
loaded between them and may be injecting GA4 as well.

Two `config` calls for the same measurement ID generally means two `page_view`
events, which inflates sessions and halves your apparent bounce rate.

**Fix** — delete the second block (lines 30–38 in the file you sent). Then open
the site with the GA Debugger extension and confirm you see one `page_view`. If
GTM is also configured with a GA4 tag, remove the hardcoded snippet entirely
and let GTM own it.

---

## 4. The Contact Form 7 ID is hardcoded in two places

`7c79e65` appears in `functions.php` (line 133) and `header.php` (line 319).
Rebuild or replace that form and the site breaks in two places, silently.

**Fix** — an ACF options field, or at minimum one constant near the top of
`functions.php`:

```php
define( 'VV_ENQUIRY_FORM_ID', '7c79e65' );
// ...
echo do_shortcode( '[contact-form-7 id="' . VV_ENQUIRY_FORM_ID . '" title="Get In Touch"]' );
```

---

## Noted, not urgent

- `header.php` prints ~3KB of inline `<style id="vv-page-banner-css">` on every
  non-front-page view. It is not separately cacheable and will conflict with the
  redesign stylesheet when that lands. Worth moving into the theme CSS as part
  of the port rather than now.
- `footer.php` carries roughly 200 lines of inline jQuery for sliders, phone
  input masking and the CF7 redirect. Same story — best handled during the port,
  not as a standalone change.
