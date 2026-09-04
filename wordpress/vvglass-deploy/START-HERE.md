# VV Glass — deployment guide

Everything in this folder, in the order to do it. About 30 minutes.

Two independent pieces, each with its own off switch:

| | What | Off switch |
|---|---|---|
| **A** | Shared sections plugin | Deactivate the plugin |
| **B** | Header, footer and homepage redesign | `VVG_REDESIGN` → `false` |

You can stop after A and come back to B another day.

---

## Before anything

- [ ] Work on **staging** if you have one.
- [ ] Back up **files and database** either way.
- [ ] Confirm **ACF PRO** is active — the repeaters need PRO.
- [ ] Have **FTP/SFTP** access. Do not use Appearance → Theme File Editor; a
      syntax error saved there can lock you out of wp-admin.
- [ ] Screenshot the current homepage, an inner page and the footer, so you
      have a before.

---

# PART A — Shared sections plugin

### A1. Upload, don't activate

**Plugins → Add New → Upload Plugin →** `1-plugin/vv-shared-sections.zip` →
Install. **Do not click Activate yet.**

### A2. Detach the old theme copy

Your `functions.php` currently ends with:

```php
require_once get_stylesheet_directory() . '/init.php';
```

1. **Delete that line.** Save.
2. **Then** rename `init.php` → `init.php.bak` in the child theme root.

> Order matters. Renaming the file while that line is still there is a fatal
> error — `require_once` on a missing file white-screens the site.

Leave the theme's `includes/`, `templates/` and `assets/` folders alone. With
the require gone, nothing loads them, and they are your fallback.

The shared section will now have vanished from the site. Correct — the theme
copy is off and the plugin is not on yet.

### A3. Activate

**Plugins → VV Shared Sections → Activate.** Then check in order:

- [ ] **Shared Sections** appears in the sidebar, with a **Page Groups**
      submenu holding Hub, Installation, Repair, Replacement, Fencing.
- [ ] Open your existing section: there is a new **Content** tab, and **every
      existing Intro / FAQ / Contact value is still filled in.** ← the one that
      matters. If a field is blank that was not before, stop and roll back.
- [ ] Set its **Order** to `10` (Page Attributes box), leave its **Page Group
      empty**. Update.
- [ ] **Purge LiteSpeed Cache**, load the site: section back, same place.
- [ ] On the **Contact** tab there is now a **Form Shortcode** field. Paste your
      Contact Form 7 shortcode there so the enquiry form appears beside the
      contact cards, as in the design. Leave it blank and the theme's form is
      used when Part B is installed; with no form at all the map stays beside
      the cards instead.

**If it does not come back:** the output hook moved to
`siteorigin_corp_footer_before`, which only fires when that page's footer is
enabled in SiteOrigin page settings. Add to `functions.php`:

```php
add_filter( 'vvss_output_hook', function () { return 'get_footer'; } );
```

**If you get a white screen:** rename the plugin folder to anything else over
FTP — WordPress deactivates a plugin whose folder vanishes — then put
`init.php` back and restore the require line.

### A4. Content

Two kinds of section, and the difference is only whether a Page Group is
ticked:

| Section | Page Group | Shows on |
|---|---|---|
| Your existing one — Intro, FAQ, Contact | **empty** | **every page** |
| Variation 1–5 — Content tab only | **one group** | only that group's pages |

The tabs follow that choice, so there is nothing to remember: a site-wide
section shows **Intro, FAQ and Contact**, a section with a Page Group shows
**Content**. Change the Page Group and Update, and the tabs change with it. A
tab that already holds copy is never hidden, and hidden fields keep their
values in the database.

Sections with a Page Group always render **above** the site-wide ones, so the
varying content sits on top of the global intro, FAQ and contact block. That is
structural — there is no Order to set and no way to get it the wrong way round.

**Shared Sections → Set Up Content → Create the sections.**

Creates the five Page Groups and the five Content sections — around 6,400
words, straight from the content document. It never overwrites: a section whose
title already exists is left alone, so running it twice is safe, and **your
existing site-wide section is not touched.**

Re-running it also repairs the questions repeater on any section an earlier
version created empty. Nothing else on an existing section is rewritten.

Then tag the pages: **Pages → All Pages**, tick the pages in a group, **Bulk
Actions → Edit → Apply**, set the Page Group, **Update**.

| Group | Pages |
|---|---|
| Hub | Home, About, Contact, and the four service hubs |
| Installation | the 6 installation sub-pages |
| Repair | the 9 repair sub-pages |
| Replacement | the 6 replacement sub-pages |
| Fencing | Glass Pool Fencing |

An untagged page just gets the site-wide block. Safe failure.

---

# PART B — Header, footer and homepage

### B1. `functions.php` first

Open `3-functions-snippet/functions-additions.php`. Copy everything **except
the opening `<?php` line** and paste it at the very end of the child theme's
`functions.php`. Save.

Nothing visible changes yet. This adds the kill switch, loads the assets, and
puts the contact details and CF7 form id in one place.

### B2. Upload the assets

Upload all three from `2-child-theme/assets/` into
`wp-content/themes/siteorigin-corp-child/assets/`:

- `vvg-redesign.css`
- `vvg-redesign.js`
- `vvg-home.css`

And both files from `2-child-theme/includes/` into `includes/`:

- `acf-home.php`
- `seed-home.php`

Still nothing visible.

### B3. Rename your originals

In the child theme root:

- `header.php` → `header-legacy.php`
- `footer.php` → `footer-legacy.php`

Do not delete them. They are what the kill switch falls back to.

### B4. Upload the new templates

`2-child-theme/header.php` and `2-child-theme/footer.php` → child theme root.

### B5. Switch the homepage to the new template

The homepage is now built from ACF fields in a dedicated template rather than
page-builder content, so the markup matches the design instead of being
restyled into shape.

1. Upload `2-child-theme/template-home.php` to the child theme root, and both
   files from `2-child-theme/includes/` into `includes/`.
2. **Shared Sections → Set Up Content**, and under **Homepage** click
   **Fill in the homepage**. Leave the checkbox ticked and it also switches the
   front page onto the template.

That fills 46 fields and 18 repeater rows with the approved copy. Anything
already filled in is skipped, so it is safe to re-run.

> **If you ran an earlier version of this and the repeater sections came out
> empty** — the trust row, the Why Choose Us points, the service cards, the
> glazing-partner cards and the How We Work cards — just run it again. The
> seeder now clears anything the earlier run stored in a format ACF could not
> read back, and refills it. Text fields you have since edited are untouched.

3. **Images are not seeded** — they have to come from the media library. Open
   **Pages → Home** and add the hero slides, the Why Choose Us image, the four
   service images, the project gallery and the About image.
4. Every section renders **only when its fields hold something**, so the page
   looks progressively more complete as you add images rather than showing
   empty shells.

Your existing WPBakery content stays in the database untouched. Switching the
template back to Default brings it straight back.

The contact block, FAQ and shared copy are **not** part of this template — the
plugin renders them above the footer on every page.

### B6. Clear the Customizer CSS ⚠

**Appearance → Customize → Additional CSS.**

That block positions the homepage hero absolutely with percentage offsets
(`.home .banner-content { top: 54.6% }`, `.banner-fixed-form { top: 55.9% }`).
It loads **after every enqueued stylesheet**, so it beats the redesign.

**Copy it into a text file first**, then clear it and Publish. The hero will
not take the new layout until you do.

### B7. Purge and check

**Purge LiteSpeed Cache**, then walk through:

- [ ] **Header** — brown bar, logo alone with no wordmark text beside it, white
      nav with gold hover, gold CTA, dark progress line filling as you scroll.
- [ ] **Dropdowns** — hover a service menu: opens, sits on screen, nothing wraps.
- [ ] **Homepage** — hero with the form on the right, Why Choose Us, service
      cards, 2×2 projects, dark glazing band, single review, About, three
      pillars, contact.
- [ ] **Click a project image** — lightbox opens, arrows and Escape work.
- [ ] **An inner page** — banner with heading, breadcrumbs, two buttons and the
      compact form.
- [ ] **Footer** — brown, gold headings, centred on mobile.
- [ ] **On a real phone** — phone circle and burger, drawer slides in, submenus
      expand, floating Call Now pill.
- [ ] **Submit the enquiry form once** and confirm it still redirects to
      `/thank-you/`.

### B8. Check Google Analytics ⚠

Your old `header.php` loaded `gtag.js` **twice**, each with its own
`gtag('config','G-T2S7QD9EQ3')` — two page_view events per visit. The new
header keeps **GTM only**.

**If GTM does not already contain a GA4 tag, add one there**, or you lose
analytics entirely. Open the site and confirm one page view in **GA →
Realtime** before you walk away.

---

## Rolling back

**Part B:** in `functions.php`, change

```php
define( 'VVG_REDESIGN', true );
```

to `false`. Purge the cache. Header, footer and homepage return to your
originals instantly. To remove it fully, rename the two `-legacy` files back
and delete the snippet.

**Part A:** deactivate the plugin, rename `init.php.bak` back to `init.php`,
and put the require line back at the end of `functions.php`.

Deactivating never deletes content — sections, field values and page tags all
stay in the database and come back on reactivation.

---

## Two things still needing a decision (not technical)

- The Word document's **warranty claim** — *"Every job we complete is backed by
  a warranty covering both our workmanship and the materials we install"* —
  appears nowhere on the current site. Across 29 pages it becomes a
  representation under Australian Consumer Law. It needs to be true and to
  state a term.
- The document says **"often on the same day"** while the site sells **24/7**
  emergency response and has a page called *24 Hour Glass Repair*.
