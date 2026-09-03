# VV Shared Sections v1.1

Adds per-page-group targeting to the existing shared sections module, so the
five content variations from the Word document can land on their own groups of
pages while the FAQ and contact details stay in one place.

Nothing here has been applied to the live site. Review, then deploy yourself.

---

## What changed

| File | Change |
|---|---|
| `init.php` | New `page_group` taxonomy, a resolver that returns every section applying to a page, `page-attributes` support for ordering, and a hook move. |
| `includes/acf-fields.php` | New **Content** tab (open copy + question list), a per-page override field group, and an `faq_open_first` toggle. All existing field keys unchanged. |
| `templates/faq-contact.php` | Renders the Content block above the existing three. |
| `assets/shared-section.css` | Content block styles; full-bleed is now opt-in. |

### How targeting works

- A section with **no** Page Group is site-wide and renders on every page.
- A section **with** a Page Group renders only on pages carrying the same term.
- A page's optional **Shared Section Override** replaces the group match for
  that page only. Site-wide sections still render.
- Several sections on one page stack by **Order** (Page Attributes), lowest first.

Verified against a fixture covering group matching, site-wide inclusion,
disabled sections, sections with no saved meta, override precedence and
ordering.

### Two behaviour changes worth knowing

**1. The automatic output moved hooks.** It was on `get_footer`, which fires
while still inside `.corp-container`, so the stylesheet needed
`width:100vw; margin-left:calc(50% - 50vw)` to break out. `100vw` includes the
scrollbar, so that overflows horizontally by the scrollbar width on Windows.

It now hooks `siteorigin_corp_footer_before`, which fires after the theme
closes `#content`, so no hack is needed. The trade-off: that action sits inside
the theme's `siteorigin_page_setting( 'footer', true )` check, so a page with
its footer switched off will not show the section. To go back:

```php
add_filter( 'vvss_output_hook', fn() => 'get_footer' );
```

**2. `intro_body` lost its default value.** The 600-word "Glass That Works
Harder" text was baked into the field as a `default_value`. Saved content is
untouched — defaults only apply to fields that have never been saved — but a
brand new section now starts empty instead of pre-filled.

---

## Deploying

1. **Back up** the site and database. Work on staging.
2. Confirm **ACF PRO** is active. The repeaters need it.
3. Replace these four files in `wp-content/themes/siteorigin-corp-child/`:
   - `init.php`
   - `includes/acf-fields.php`
   - `templates/faq-contact.php`
   - `assets/shared-section.css`
4. Load any admin page once. The `page_group` taxonomy registers and seeds five
   terms (Hub, Installation, Repair, Replacement, Fencing).
5. Check the existing shared section still renders, then purge LiteSpeed Cache.

`functions.php` needs no change — it already does `require_once
get_stylesheet_directory() . '/init.php';`.

---

## Setting up the content

**Your existing section** — leave its Page Group empty. It stays site-wide and
keeps holding the intro, FAQ and contact details. Set its **Order** to `10`.

**Then, for each of the five variations:**

1. Shared Sections → Add New. Title it "Variation 1 — Hub" and so on.
2. Fill only the **Content** tab: heading, opening copy, the questions, closing
   copy, CTA.
3. Tick its **Page Group** in the sidebar.
4. Set **Order** to `0` so it renders above the site-wide block.

**Then tag the pages.** Pages → All Pages, tick the ones in a group, Bulk
Actions → Edit, set the Page Group, Update. Roughly:

| Group | Pages |
|---|---|
| Hub | Home, About, Contact, and the four service hubs |
| Installation | The 6 installation sub-pages |
| Repair | The 9 repair sub-pages |
| Replacement | The 6 replacement sub-pages |
| Fencing | Glass Pool Fencing |

Any page you forget to tag simply gets the site-wide block, which is a safe
failure.

---

## Still outstanding

Two content questions block go-live, and neither is technical:

- The **warranty claim** in the Word document ("Every job we complete is backed
  by a warranty covering both our workmanship and the materials we install")
  appears nowhere on the current site. Published across 29 pages it becomes a
  representation under Australian Consumer Law. It needs to be true, and it
  needs a stated term.
- The document says **"often on the same day"** while the site sells **24/7**
  emergency response and has a page called *24 Hour Glass Repair*.

Separately: putting Variation 5, the cost FAQ, on Glass Pool Fencing alone
spends your most useful content on one page, while the one page with genuinely
distinct requirements (AS 1926.1 compliance, gate latches, 1200mm heights)
gets generic copy. Worth revisiting the mapping.
