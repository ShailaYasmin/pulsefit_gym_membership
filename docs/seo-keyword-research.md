# PulseFit Gym — SEO Keyword Research

ICT726 Assignment 4 — documents the keyword research and on-page SEO strategy applied across the site, per the "Advanced SEO Techniques" requirement.

## Method

Keywords were chosen by thinking through what someone actually types into Google at each stage of deciding on a gym, rather than guessing single generic terms. Three intent groups were used:

1. **Commercial/transactional** — someone close to signing up ("gym membership Sydney", "join a gym near me").
2. **Informational** — someone still comparing options ("best gym for beginners Sydney", "gym with personal training Sydney").
3. **Long-tail/specific** — someone with a narrow need, which is usually easier to rank for and converts well ("24/7 gym access Sydney CBD", "gym with kids club Sydney", "HIIT classes Sydney").

A real keyword-planning pass would use a tool like Google Keyword Planner or Ubersuggest for search-volume data; for this coursework project the same structured thinking was applied manually, since the business itself is fictional and has no real search data to pull.

## Primary keywords (used in `<title>` and the main `<h1>`)

| Page | Primary keyword | Where it appears |
|---|---|---|
| Home | gym membership Sydney | Title tag, meta description, H1 area copy |
| Membership | gym membership plans Sydney | Title tag, meta description, H1 |
| About | boutique gym Sydney | Title tag, H1 |
| Contact | join a gym Sydney | Title tag, meta description |
| Gallery | Sydney gym photos / training floor | Title tag |
| Testimonials | Sydney gym reviews | Title tag, meta description |

## Secondary / long-tail keywords (used in headings, body copy and image alt text)

- "24/7 gym access" — Home hero stats, Membership Premium plan
- "personal training Sydney" — Membership add-ons, About trainer bios
- "HIIT classes Sydney" — Membership schedule, Gallery captions
- "small group fitness classes" — Home features section
- "gym with childcare" — Membership add-ons (Kids Club)
- "no lock-in contract gym" — Membership page subheading and FAQ
- "free gym trial Sydney" — Home and Gallery CTA bands

## On-page SEO techniques implemented

- **Unique, descriptive `<title>` and meta description on every page** — generated per-page via `$pageTitle` / `$pageDescription` in `includes/header.php`, never a repeated generic title.
- **One `<h1>` per page**, matching the page's primary keyword, with a logical `h2`/`h3` hierarchy underneath (verified — no heading levels are skipped).
- **Descriptive, keyword-relevant image `alt` text** on every image (e.g. *"Woman performing battle rope waves during an outdoor rooftop HIIT session"* rather than "gallery-6.jpg"), which also serves accessibility.
- **Canonical URLs** on every page (`<link rel="canonical">`) to avoid duplicate-content issues.
- **Open Graph and Twitter Card meta tags** so links shared on social media render with a proper title, description and image.
- **Structured data (JSON-LD, schema.org `ExerciseGym`)** with name, address, phone and opening hours, so Google can potentially show a rich result (map card, hours, rating) directly in search.
- **`robots.txt`** allowing crawling of all public pages while explicitly disallowing the private `/admin/` and `/member/` areas and internal `/includes/` and `/sql/` folders.
- **`sitemap.php`** listing every public, indexable page with a `changefreq`/`priority` hint, referenced from `robots.txt`.
- **Semantic HTML5** (`header`, `nav`, `main`, `article`, `section`, `footer`) instead of generic `div` soup, which search engines use as a structural signal.
- **Friendly, readable URLs** (`membership.php`, `about.php`) rather than query-string-only routes.
- **Fast-loading, optimised images** — all photography is pre-compressed and served at the display size rather than full camera resolution (carried over from the Assignment 3 static build).
