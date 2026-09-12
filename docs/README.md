# PulseFit Gym — Dynamic Website

ICT726 Web Development — Assignment 4 (Dynamic Website). A PHP + MySQL rebuild of the PulseFit Gym static site (Assignment 3), adding real user accounts, role-based access control, a relational database, and admin/member back-office tools.

## Tech stack

- **PHP 8.5** (procedural, no framework — as required by the brief)
- **MySQL 8** via **PDO** with prepared statements throughout
- Plain **HTML5 / CSS3 / vanilla JavaScript** for the front end (same design system as Assignment 3)
- No external libraries or frameworks on either side

## Local setup

1. Create the database and load the schema:
   ```
   mysql -u root < sql/schema.sql
   ```
2. Load seed data — **must** use the utf8mb4 client charset flag, or the MySQL CLI's latin1 default will corrupt the accented punctuation in the seed text:
   ```
   mysql --default-character-set=utf8mb4 -u root < sql/seed.sql
   ```
3. Create a dedicated, least-privilege database user (the app never connects as root):
   ```sql
   CREATE USER 'pulsefit_app'@'localhost' IDENTIFIED BY 'PulseFit_App_2026!';
   GRANT SELECT, INSERT, UPDATE, DELETE ON pulsefit_gym.* TO 'pulsefit_app'@'localhost';
   ```
   (Update `includes/config.php` if you use different credentials.)
4. Seed demo accounts (hashed passwords, a sample active membership, and two sample bookings):
   ```
   php sql/seed_users.php
   ```
5. Run the app with PHP's built-in server from the project root:
   ```
   php -S localhost:8010
   ```

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@pulsefitgym.example | Admin123! |
| Member (active Standard plan) | member@pulsefitgym.example | Member123! |
| Normal (registered, no plan yet) | alex@pulsefitgym.example | Normal123! |

## Project structure

```
├── index.php, about.php, membership.php, gallery.php,      Public pages
│   testimonials.php, contact.php, privacy.php
├── register.php, login.php, logout.php, select-plan.php     Auth + plan selection
├── sitemap.php, robots.txt                                  SEO
├── 403.php                                                  Access-denied page
├── includes/
│   ├── config.php        DB credentials + site constants
│   ├── db.php             PDO connection (utf8mb4, exceptions, no emulated prepares)
│   ├── auth.php            Sessions, login/logout, require_login(), require_role()
│   ├── functions.php       Escaping, CSRF, flash messages, formatting helpers
│   ├── header.php / footer.php   Shared layout (nav, meta tags, JSON-LD, footer)
│   └── member-nav.php / admin-nav.php   Dashboard sidebar partials
├── member/
│   ├── dashboard.php        Overview: plan, upcoming bookings, stats
│   ├── book-class.php       Book a class (date picker, capacity check)
│   ├── my-bookings.php      View/cancel bookings
│   └── profile.php          Edit profile / change password
├── admin/
│   ├── dashboard.php        Studio-wide stats
│   ├── plans.php            CRUD: membership plans + their features
│   ├── classes.php          CRUD: weekly class schedule
│   ├── members.php          Change member role/status
│   ├── enquiries.php        Triage contact-form submissions
│   └── testimonials.php     Approve/reject member reviews
├── sql/
│   ├── schema.sql           Table definitions
│   ├── seed.sql              Sample plans, classes, trainers, testimonials
│   └── seed_users.php        Demo accounts (hashed passwords via PHP)
├── css/style.css, js/script.js, images/     Reused from the Assignment 3 static build
└── docs/                     This file, database schema notes, SEO keyword research
```

## Database schema

Nine tables (see `sql/schema.sql` for full column definitions and foreign keys):

- **users** — accounts; `role` is `admin` / `member` / `normal`, `password_hash` via `password_hash()`.
- **membership_plans** + **plan_features** — the pricing tiers and their feature lists (1-to-many).
- **user_memberships** — which plan a user is/was subscribed to, with `start_date`/`end_date`/`status`.
- **trainers** — coaching staff, shown on the About page and linked from classes.
- **classes** — the weekly schedule (day, time, trainer, capacity).
- **bookings** — a member's reservation for a specific date's class occurrence; a unique constraint on `(user_id, class_id, booking_date)` stops double-booking.
- **enquiries** — Contact page submissions.
- **testimonials** — member-submitted reviews with an admin moderation `status`.

Foreign keys use `RESTRICT` (not `CASCADE`) wherever deleting the parent row would silently destroy history that matters — e.g. you cannot delete a membership plan while members are subscribed to it, and you cannot delete a class that has existing bookings. Both are caught and shown to the admin as a friendly message rather than a database error.

## Key functionality (mapped to the assignment brief)

| Requirement | Where |
|---|---|
| Register / log in / log out | `register.php`, `login.php`, `logout.php` |
| Role-based access levels | `includes/auth.php` (`require_role()`), enforced on every member/admin page |
| Hashed passwords | `password_hash()` / `password_verify()`, never plain text |
| CRUD via PHP + MySQL | Plans, classes, bookings, enquiries, testimonials — all four operations, all through PDO prepared statements |
| ≥2 validated input forms | Register, Login, Contact, Testimonial submission, Class booking, Profile edit, Admin plan/class editors — seven in total |
| Error handling & validation | Server-side validation on every form, with field-level messages; HTML5 attributes as a first pass |
| Responsive / cross-browser | Same CSS design system and breakpoints as Assignment 3 |
| Accessibility | Semantic HTML5, ARIA labels on icon-only controls, skip link, visible focus states |
| Image/media optimisation | Reused pre-compressed images from Assignment 3 |
| Privacy & ethics | `privacy.php` — plain-language notice covering what's collected, why, and how it's protected |
| SEO | Per-page meta tags, canonical URLs, Open Graph, JSON-LD, `sitemap.php`, `robots.txt` — see `docs/seo-keyword-research.md` |
| Secure data handling | Password hashing, CSRF tokens on every form, parameterised queries (no string-built SQL anywhere), session regeneration on login, least-privilege DB user |

## A real bug found during testing

The original `sql/seed.sql` import (`mysql -u root < sql/seed.sql`) silently double-encoded the handful of rows containing an em/en dash, because the MySQL CLI's default connection charset is `latin1` unless told otherwise, even though the database and columns are `utf8mb4`. This produced garbled text (`â€"` instead of `–`) on the live pages. It was caught by comparing the rendered page text against the source data, root-caused by inspecting the raw response bytes with `curl | xxd`, fixed by rewriting the affected rows through PDO (which uses the correct charset — see `includes/db.php`), and documented in `sql/seed.sql` so it can't happen again on a fresh import. See `sql/fix_encoding.php` for the fix script.

## Individual contribution

This is listed as a group assessment (4 students) in the brief. The codebase above was built as a complete, working submission; how contribution/authorship is divided and documented for the "Individual Contribution" submission is between the team and hasn't been filled in here, since that depends on the real team's actual work split.
