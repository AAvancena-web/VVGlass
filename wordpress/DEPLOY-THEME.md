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

## ⚠ One manual step: clear the Customizer's Additional CSS

**Appearance → Customize → Additional CSS.** That block currently positions the
homepage hero absolutely with percentage offsets:

```css
.home .banner-content { position:absolute; top:54.6%; ... }
.banner-fixed-form    { position:absolute; top:55.9%; right:0; ... }
```

It loads **after every enqueued stylesheet**, so it beats the redesign. Copy it
somewhere safe, then clear it. The hero will not take the new layout until you
do.

---

## Homepage

`vvg-home.css` loads on the front page only, so inner pages carry none of its
weight. It restyles the WPBakery markup that is already in the database rather
than rebuilding any pages — **no page content needs editing.**

Four faults surfaced by rendering against your actual `style.css`, all fixed:

- `.choose_class` and `.clear_row` lay their columns out with `float:left`, and
  the float was never cleared. Each row collapsed to 192px and the next section
  was pulled up beside it — `.fully_row` was rendering 640px wide instead of
  1360.
- With that fixed the columns still wrapped: 50% plus 40px of padding measures
  720px each under content-box, so the pair totalled 1440 in a 1360 container.
- Service card links rendered as default blue underlined text.
- The testimonial card measured 1442px inside a 1360px container.

Two things turned out easier than expected: your projects grid is **already**
`1fr 1fr`, so it only needed larger 3:2 images and the lightbox rather than a
WPBakery rebuild; and `.corp-container` at 87.5% was 1323px at 1512px wide,
which did not line up with the header — both now use the same 1440/1880 spec.

The lightbox binds to the existing WPBakery `<img>` elements, with prev/next,
a counter, arrow keys, Escape and focus return.

---

## Rollback

Set `VVG_REDESIGN` to `false` in `functions.php` and purge the cache. That is
it. To remove it entirely, rename the two legacy files back over the new ones
and delete the snippet.
