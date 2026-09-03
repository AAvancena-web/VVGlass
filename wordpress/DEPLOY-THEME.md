# Deploying the header & footer redesign

Unlike the plugin, these are theme files — there is no Deactivate button. So
the first thing this adds is a kill switch.

**Backup the child theme folder before you start.** Staging strongly preferred.

---

## The kill switch

`functions.php` gets this constant:

```php
define( 'VVG_REDESIGN', true );
```

Flip it to `false` and `header.php` / `footer.php` hand straight back to
`header-legacy.php` / `footer-legacy.php` — your originals, renamed and
otherwise untouched — and the redesign stylesheet stops loading. One line, full
revert, no FTP juggling.

---

## Files

| Action | File | Destination |
|---|---|---|
| **Rename** | `header.php` → `header-legacy.php` | child theme root |
| **Rename** | `footer.php` → `footer-legacy.php` | child theme root |
| **Add** | `wordpress/theme/header.php` | child theme root |
| **Add** | `wordpress/theme/footer.php` | child theme root |
| **Add** | `wordpress/theme/assets/vvg-redesign.css` | `assets/` |
| **Add** | `wordpress/theme/assets/vvg-redesign.js` | `assets/` |
| **Append** | `wordpress/theme/snippets/functions-additions.php` | end of `functions.php` |

---

## Order

**1. `functions.php` first.** Paste the contents of
`snippets/functions-additions.php` at the end, **without** its opening `<?php`
tag — your file already has one. Save. Nothing visible changes yet.

**2. Upload `assets/vvg-redesign.css` and `assets/vvg-redesign.js`.**
Still nothing visible.

**3. Rename** `header.php` → `header-legacy.php` and `footer.php` →
`footer-legacy.php`.

**4. Upload the new `header.php` and `footer.php`.**

**5. Purge LiteSpeed Cache**, then load the site.

Doing `functions.php` first matters: the templates look for
`vvg_redesign_active()`, and without it they render the redesign markup with no
stylesheet behind it.

---

## What to check

- **Header**: brown bar, logo alone (no wordmark text beside it), nav in white
  with gold hover, gold CTA button, dark progress line along the bottom edge
  that fills as you scroll.
- **Dropdowns**: hover a service menu — panel opens, sits on screen, no wrapped
  items.
- **Mobile** (real phone, not just a narrow window): phone circle and burger,
  drawer slides in, service submenus expand, floating Call Now pill at the
  bottom.
- **Inner page banner**: heading, breadcrumbs, two buttons, and the enquiry
  form in two columns — Name+Email, Phone+Suburb, Message full width.
- **Footer**: brown, gold widget headings, centred on mobile.
- **The homepage is unchanged.** That is expected — see below.

---

## Things worth knowing

**Units are px, not rem.** The mock used `rem` in 82 places. Your theme sets
`html{font-size:calc(...)}`, which collapses the root below ~10px under 1200px,
so a straight port would have shrunk every size on tablet and mobile. The
shared-sections stylesheet already worked around this the same way.

**New markup uses `vvg-` class names**, not the theme's `.site-header` /
`.main-navigation`. That means the theme's own header CSS simply does not
apply, instead of being fought with `!important`.

**Two GA snippets became one.** `header.php` loaded `gtag.js` twice, each with
its own `gtag('config','G-T2S7QD9EQ3')` — two page_view events per load. The
new header keeps GTM only. **If GTM does not currently contain a GA4 tag, add
one there**, or you will lose GA entirely. Check in GA Realtime after deploying.

**Two obsolete scroll handlers were dropped** from `footer.php` — they added a
`.fixed` class to a sticky-by-CSS header on every scroll event. Everything else
in that file is unchanged: the sliders, the tel filter, the CF7 redirect and
the show-more toggle all still run, because the homepage still depends on them.

**The CF7 id is now `VVG_ENQUIRY_FORM_ID`**, defined once instead of hardcoded
in two files.

---

## Still to do: the homepage body

The header, footer, inner-page banners and shared sections are done. The
homepage **body** is not, and it is a different kind of job: those sections are
not in any template file, they are WPBakery content in the database
(`.choose_class`, `.our-service`, `.project_row`, `.glass_service`).

Most of it can be restyled with CSS against that existing markup. Two pieces
cannot, because they need different markup:

- the 2×2 project grid with the lightbox
- the compact banner form on the hero

Those need the page rebuilt in WPBakery, not just restyled.

To do that cleanly I need the child theme's **`style.css`** — I want to extend
what is there rather than override it.

---

## Rollback

Set `VVG_REDESIGN` to `false` in `functions.php` and purge the cache. That is
it. To remove it entirely, rename the two legacy files back over the new ones
and delete the snippet.
