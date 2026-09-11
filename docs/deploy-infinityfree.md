# Deploying to InfinityFree

This branch (`shaila_dynamic_live_site`) holds the changes needed to run the
site on InfinityFree free PHP hosting (`pulsefit-gym-membership.freepage.cc`),
instead of the local dev server. `main` and `shaila_dynamic` are untouched —
they still point at the local `pulsefit_gym` database.

## What changed on this branch

- `includes/config.php` — DB credentials point at InfinityFree's MySQL server
  instead of `localhost`.
- `sql/hosting_import.sql` — `sql/schema.sql` + `sql/seed.sql` merged into one
  file with the `CREATE DATABASE`/`USE` statements removed, so it can be
  imported straight into InfinityFree's pre-created database through
  phpMyAdmin (there's no CLI/SSH access on free hosting).
- `includes/.htaccess`, `sql/.htaccess`, `docs/.htaccess` — block direct web
  access to these folders. The local PHP dev server only serves `.php` files
  you actually route to, but a real Apache host will serve *any* file by
  default, so without this someone could browse straight to
  `includes/config.php` or `sql/hosting_import.sql` and read the DB password.

## 1. Create the database

Already done — the InfinityFree control panel confirms the database exists
(the full name should look like `if0_42890673_pulsefit`, the username
prefixed onto whatever you typed).

**Please confirm the exact database name shown on your MySQL Databases
panel.** `includes/config.php` on this branch currently assumes it's
`if0_42890673_pulsefit`. If yours differs, that constant needs updating
before anything will connect.

## 2. Set the PHP version

In the InfinityFree client area, open **PHP Version Manager** for this
domain and select **PHP 8.1** (or newer). The code uses `declare(strict_types=1)`
and `str_starts_with()` in [admin/plans.php](admin/plans.php:69), both of
which need PHP 8.0+. Free hosting sometimes defaults to an older version, so
check this explicitly rather than assuming.

## 3. Upload the files

Use the InfinityFree **File Manager** (in the control panel) or an FTP
client (e.g. FileZilla, using the FTP credentials from the control panel).
Upload the contents of `gym_membership/` into `htdocs/` — not the
`gym_membership` folder itself, its *contents*.

Skip these — they're git/dev-only and don't belong on the live server:
- `.git/` (the whole folder)
- `.gitignore`, `.DS_Store`
- `images/.DS_Store` if present

Everything else (all the `.php` files, `css/`, `js/`, `images/`, `includes/`,
`sql/`, `docs/`, the three new `.htaccess` files) should be uploaded.

## 4. Import the database

1. In the InfinityFree control panel, open **phpMyAdmin**.
2. Select your database in the left sidebar.
3. Click the **Import** tab.
4. Choose `sql/hosting_import.sql`, set **Character set of the file** to
   `utf8mb4` (important — this is what avoids the dash-encoding bug
   documented in `docs/README.md`), and click **Go**.

This creates all 9 tables and loads the plans, trainers, classes, and
approved testimonials. It does **not** create user accounts — passwords must
be hashed by PHP, not written as plain SQL text.

## 5. Seed the demo accounts

`sql/seed_users.php` does this, but it's inside the now-protected `sql/`
folder, so it needs to run once *before* `sql/.htaccess` is active:

1. In File Manager, temporarily rename `sql/.htaccess` to
   `sql/.htaccess.disabled`.
2. Visit `https://pulsefit-gym-membership.freepage.cc/sql/seed_users.php`
   in your browser once. You should see "Seeding demo accounts..." followed
   by three "created ..." lines and "Done."
3. Rename `sql/.htaccess.disabled` back to `sql/.htaccess` immediately
   afterward, so the folder is protected again.

(The script is safe to run more than once — it skips any email that already
exists — but there's no reason to leave the folder open longer than needed.)

## 6. Test it

Visit the live site and log in with the demo accounts (from
`docs/README.md`):

| Role | Email | Password |
|---|---|---|
| Admin | admin@pulsefitgym.example | Admin123! |
| Member | member@pulsefitgym.example | Member123! |
| Normal | alex@pulsefitgym.example | Normal123! |

Also try: registering a new account, booking a class, submitting the contact
form, and — as admin — editing a plan.

## A note on local testing

InfinityFree's MySQL servers only accept connections that originate from
InfinityFree's own hosting — not from your laptop. That means once
`includes/config.php` points at `sql207.infinityfree.com`, running the app
with `php -S localhost:8010` on this branch **won't work locally**; the DB
connection will simply fail. That's expected. Test this branch's database
behaviour on the live site, not locally. (`main` and `shaila_dynamic` still
work locally against `pulsefit_gym` exactly as before.)

## A note on the committed credentials

`includes/config.php` on this branch contains the InfinityFree DB password
in plain text, the same pattern the local dev config already uses for the
`pulsefit_app` MySQL user. This is consistent with how the rest of the
project handles DB credentials, but it does mean anyone with access to this
GitHub repo can read the live database password. If that's a concern once
the assignment is marked, rotate the InfinityFree database password from
the control panel — the app will keep working, you'd just also need to
update this file to match.
