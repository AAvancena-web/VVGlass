# Deployment guide — VV Shared Sections v1.1

Follow in order. Steps 1–3 take about ten minutes; step 4 is the content work.

**All four files are overwrites. Nothing new is created.** Your theme already
has all of them.

---

## Before you start

- [ ] Do this on **staging**, not production.
- [ ] Back up files **and** the database.
- [ ] Confirm **ACF PRO** is active (Plugins → Installed). The repeaters need
      PRO; the free version will show the fields but not the repeaters.
- [ ] Open the live site and note what the shared section currently looks like,
      so you have something to compare against.

---

## Where the files go

Your `functions.php` ends with:

```php
require_once get_stylesheet_directory() . '/init.php';
```

`get_stylesheet_directory()` is the **child theme root**, so `init.php` sits at
the top level of the theme — not inside a `vv-shared-sections/` folder, despite
what the comment block at the top of that file says. The other three are in
subfolders relative to it.

| From this repo | Goes to |
|---|---|
| `wordpress/vv-shared-sections/init.php` | `wp-content/themes/siteorigin-corp-child/init.php` |
| `wordpress/vv-shared-sections/includes/acf-fields.php` | `wp-content/themes/siteorigin-corp-child/includes/acf-fields.php` |
| `wordpress/vv-shared-sections/templates/faq-contact.php` | `wp-content/themes/siteorigin-corp-child/templates/faq-contact.php` |
| `wordpress/vv-shared-sections/assets/shared-section.css` | `wp-content/themes/siteorigin-corp-child/assets/shared-section.css` |

⚠️ Do **not** copy the `vv-shared-sections` folder itself into the theme. Copy
the four files to the paths above. Dropping the folder in would leave
`functions.php` loading the old `init.php` at the root, and nothing would change.

---

## Step 1 — Get the files

Branch `claude/wordpress-site-redesign-j7zd4i`, folder `wordpress/vv-shared-sections/`.

Either clone it:

```bash
git clone -b claude/wordpress-site-redesign-j7zd4i \
  https://github.com/AAvancena-web/VVGlass.git
cd VVGlass/wordpress/vv-shared-sections
```

or download each of the four from GitHub's web UI (Raw → Save As).

---

## Step 2 — Rename the current files, don't delete them

Over FTP/SFTP or your host's file manager, in the child theme:

```
init.php                     ->  init.php.bak
includes/acf-fields.php      ->  includes/acf-fields.php.bak
templates/faq-contact.php    ->  templates/faq-contact.php.bak
assets/shared-section.css    ->  assets/shared-section.css.bak
```

Renaming rather than deleting means rollback is renaming them back.

⚠️ `.bak` is important. If you leave a copy named `init-old.php` in the theme
it does no harm, but anything ending `.php` that WordPress or a scanner picks
up is best avoided. `.bak` is not executed.

---

## Step 3 — Upload the four new files

Upload to the paths in the table above. Then, in this order:

1. **Load any wp-admin page once.** This registers the taxonomy and creates the
   five Page Group terms.
2. Check the sidebar: **Shared Sections** should now show a **Page Groups**
   submenu with Hub, Installation, Repair, Replacement and Fencing.
3. Open **Shared Sections** → your existing section. You should see a new
   **Content** tab, and all your existing Intro / FAQ / Contact values still in
   place. If any field is blank that wasn't before, stop and roll back.
4. Set that section's **Order** to `10` (Page Attributes box). Leave its Page
   Group **empty** so it stays site-wide. Update.
5. **Purge LiteSpeed Cache** (toolbar → LiteSpeed Cache → Purge All).
6. Load the front end. The section should look the same as before, and sit in
   the same place.

### If the section disappears

Almost certainly the hook change. That block now renders on
`siteorigin_corp_footer_before`, which only fires when the page's footer is
enabled in SiteOrigin page settings. Add this to `functions.php` to go back to
the old hook:

```php
add_filter( 'vvss_output_hook', function () { return 'get_footer'; } );
```

### If you get a white screen

Rename the four `.bak` files back. That is a complete rollback — no database
changes have happened at this point.

---

## Step 4 — Set up the content

Only after step 3 is verified.

### 4a. Create the five variation sections

For each variation in the Word document:

1. **Shared Sections → Add New**
2. Title it clearly: `Variation 1 — Hub`, `Variation 2 — Installation`, etc.
3. Fill in **only the Content tab**:
   - Eyebrow, Heading
   - Opening Copy — the paragraphs before the questions
   - Questions Heading — e.g. "Questions We Get Asked Often"
   - Questions — one row per Q&A
   - Closing Copy — the final paragraph
   - CTA label and URL
4. Leave the **Intro, FAQ and Contact tabs empty.** Empty blocks render nothing,
   which is what keeps the phone number in one place.
5. In the sidebar, tick its **Page Group**.
6. Set **Order** to `0` so it renders above the site-wide block.
7. Publish.

### 4b. Tag the pages

**Pages → All Pages.** Tick every page in a group, **Bulk Actions → Edit →
Apply**, set the Page Group, **Update**.

| Group | Pages |
|---|---|
| Hub | Home, About, Contact, Glass Installation, Glass Repair, Glass Replacement, Glass Fencing |
| Installation | Residential, Commercial, Industrial, Window Installations, Custom, Shopfront |
| Repair | Emergency, 24 Hour, Residential, Commercial, Industrial, Strata, Real Estate, Window Repairs, Shopfront |
| Replacement | Home, Residential, Commercial, Industrial, Window, Shopfront |
| Fencing | Glass Pool Fencing |

A page you forget to tag just gets the site-wide block. Safe failure.

### 4c. Check

Load one page from each group. You should see the right variation, then the
intro, FAQ and contact block below it. Purge the cache first, or you will be
looking at a cached copy and think it failed.

---

## Step 5 — The theme fixes (separate day)

`THEME-FIXES.md` covers four issues in `header.php` and `functions.php`. Deploy
those on their own, after the above is settled, so that if something breaks you
know which change caused it.

---

## Rolling back after step 4

If you need to undo once content exists:

1. Rename the four `.bak` files back over the new ones.
2. The five variation sections stay in the database but stop rendering, because
   the old code only ever shows one section.
3. The `page_group` terms stay too. Harmless. Delete them under Shared Sections
   → Page Groups if you want them gone.

Nothing in this change deletes or rewrites existing content.
