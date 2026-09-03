# Deployment guide — VV Shared Sections v1.1 (plugin)

Runs as a plugin, so **Deactivate is the rollback**. No theme files are
replaced, and deactivating never deletes content.

Steps 1–4 take about ten minutes. Step 5 is the content work.

---

## Before you start

- [ ] Do this on **staging** first if you have one.
- [ ] Back up files **and** the database anyway.
- [ ] Confirm **ACF PRO** is active (Plugins → Installed). The repeaters need
      PRO; the free version shows the other fields but not the repeaters.
- [ ] Open the live site and note what the shared section looks like now, so
      you have something to compare against.

---

## The one thing that can break the site

Your `functions.php` ends with:

```php
require_once get_stylesheet_directory() . '/init.php';
```

Two traps, both fatal, both avoided by following the order in step 3:

1. **Delete the theme's `init.php` while that line is still there** →
   `require_once` on a missing file is a fatal error. White screen.
2. **Activate the plugin while the theme's original `init.php` still loads** →
   every function gets declared twice. Fatal error.

That line is the only reference to those files anywhere in the theme —
`header.php` and `footer.php` call no `vvss_` function — and `functions.php` is
the only file WordPress auto-loads. So removing the line fully detaches the old
copy.

---

## Step 1 — Get the files

Branch `claude/wordpress-site-redesign-j7zd4i`:

```
wordpress/plugin/vv-shared-sections/     ← the plugin
wordpress/theme-shim/init.php            ← the no-op replacement
```

```bash
git clone -b claude/wordpress-site-redesign-j7zd4i \
  https://github.com/AAvancena-web/VVGlass.git
```

---

## Step 2 — Upload the plugin (do not activate yet)

Copy the whole folder so you end up with:

```
wp-content/plugins/vv-shared-sections/
├── vv-shared-sections.php
├── includes/acf-fields.php
├── templates/faq-contact.php
└── assets/shared-section.css
```

Or zip the `vv-shared-sections` folder and use **Plugins → Add New → Upload
Plugin**. Either way, **do not activate yet.**

It should now appear in the Plugins list as *VV Shared Sections*, inactive.

---

## Step 3 — Detach the theme copy

Two ways. **A is cleaner and is what I would do.**

### Option A — remove the require line (recommended)

Edit `functions.php` **over FTP/SFTP, not Appearance → Theme File Editor** — a
syntax error saved through the editor can lock you out of wp-admin, and you
would need FTP to undo it anyway.

Delete the last line of the file:

```php
require_once get_stylesheet_directory() . '/init.php';
```

Save. Nothing else in `functions.php` changes.

Then rename `init.php` → `init.php.bak` in the theme. With the require gone
this is safe, and it keeps the old code as a fallback. `includes/`,
`templates/` and `assets/` in the theme can stay where they are; nothing loads
them now. Tidy them up once you are confident.

**Order matters: remove the line first, rename the file second.** Renaming
first, while the require is still there, is the white-screen case.

### Option B — leave functions.php alone

If you would rather not touch `functions.php` at all, upload
`wordpress/theme-shim/init.php` over the theme's `init.php` (rename the
original to `init.php.bak` first). It is an empty file, so the `require_once`
still succeeds and only the plugin declares anything.

Slightly less tidy — you are left with a vestigial file — but it avoids editing
`functions.php`.

### Either way

Reload the front end. **The shared section will have disappeared.** That is
correct and expected — the theme code is detached and the plugin is not on yet.

---

## Step 4 — Activate

**Plugins → VV Shared Sections → Activate.** Then check, in order:

1. **Shared Sections** appears in the admin sidebar, with a **Page Groups**
   submenu containing Hub, Installation, Repair, Replacement, Fencing.
2. Open your existing section. There is a new **Content** tab, and every
   existing Intro / FAQ / Contact value is still there. *If any field is blank
   that was not before, stop and roll back.*
3. Set its **Order** to `10` (Page Attributes box). Leave its **Page Group
   empty** so it stays site-wide. Update.
4. **Purge LiteSpeed Cache** (toolbar → LiteSpeed Cache → Purge All).
5. Load the front end. The section is back, looking as it did, in the same place.

### If the section does not come back

Most likely the hook change. It now renders on
`siteorigin_corp_footer_before`, which only fires when that page's footer is
enabled in SiteOrigin page settings. Add to `functions.php`:

```php
add_filter( 'vvss_output_hook', function () { return 'get_footer'; } );
```

### If you get a white screen

Deactivate the plugin. If you cannot reach wp-admin, rename the plugin folder
to `vv-shared-sections-off` over FTP — WordPress deactivates a plugin whose
folder has vanished.

---

## Step 5 — Set up the content

Only once step 4 is verified.

### 5a. Create the five variation sections

For each variation in the Word document:

1. **Shared Sections → Add New**
2. Title it clearly: `Variation 1 — Hub`, `Variation 2 — Installation`, …
3. Fill in **only the Content tab**: eyebrow, heading, opening copy, the
   questions, closing copy, CTA label and URL.
4. Leave **Intro, FAQ and Contact empty.** Empty blocks render nothing — that
   is what keeps the phone number in exactly one place.
5. Tick its **Page Group** in the sidebar.
6. Set **Order** to `0` so it renders above the site-wide block.
7. Publish.

### 5b. Tag the pages

**Pages → All Pages.** Tick every page in a group → **Bulk Actions → Edit →
Apply** → set the Page Group → **Update**.

| Group | Pages |
|---|---|
| Hub | Home, About, Contact, Glass Installation, Glass Repair, Glass Replacement, Glass Fencing |
| Installation | Residential, Commercial, Industrial, Window Installations, Custom, Shopfront |
| Repair | Emergency, 24 Hour, Residential, Commercial, Industrial, Strata, Real Estate, Window Repairs, Shopfront |
| Replacement | Home, Residential, Commercial, Industrial, Window, Shopfront |
| Fencing | Glass Pool Fencing |

A page you forget to tag simply gets the site-wide block. Safe failure.

### 5c. Check

Load one page from each group. Purge the cache first, or you will be looking at
a cached copy and conclude it failed.

---

## What deactivating actually does

| | Effect |
|---|---|
| Front end | Sections stop rendering immediately. |
| Admin | Shared Sections and Page Groups menus disappear. |
| Your content | **Untouched.** Sections stay as posts, field values stay in postmeta, page tags stay in the taxonomy tables. |
| Reactivating | Everything returns exactly as it was. |

There is no uninstall routine that deletes data, deliberately. If you ever want
the data gone it has to be removed by hand.

---

## Full rollback

1. Deactivate the plugin.
2. In the child theme, rename `init.php.bak` back to `init.php`.
3. If you took **Option A**, put the line back at the end of `functions.php`:
   `require_once get_stylesheet_directory() . '/init.php';`
   If you took **Option B**, delete the shim first so the original is in place.
4. Purge the cache.

You are back to exactly the current live behaviour. The five variation sections
and page tags stay in the database, dormant, ready if you try again.

---

## Step 6 — The theme fixes (separate day)

`THEME-FIXES.md` covers four issues in `header.php` and `functions.php`. Those
are genuine theme edits with no deactivate switch, so do them on their own,
after this is settled.
