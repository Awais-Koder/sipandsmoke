# Sip & Smoke

The Sip & Smoke public site and its admin control room, built as a Laravel 12
application with Livewire 3. The original frontend design, typography, spacing
and interactions are preserved; the content is now database-driven.

## What is included

- **Public site** — `/`, `/menu`, `/gallery`. Blade views, the original CSS and
  JavaScript, GSAP/Lenis motion, the menu deck, gallery pagination and lightbox.
- **Admin only auth** — `/admin/login`, rate-limited session auth, no public
  registration. Admin routes are protected by `auth` + `admin` middleware.
- **Authenticator 2FA** — after the password step, admins enrol a TOTP
  authenticator on first sign-in and verify a 6-digit code on every later
  sign-in. Secrets are encrypted, and the QR code is generated with
  `bacon/bacon-qr-code`.
- **Content editor** — more than 100 fields grouped into Brand & SEO, contact,
  navigation, hero, home sections, contact panel, menu page, gallery page and
  footer. Logo, favicon, hero, about and social images can be replaced.
- **Menu pages** — create, edit, reorder, hide/delete and upload replacement
  sheets. Every upload is converted to compressed WebP.
- **Gallery** — batch upload up to 20 photos at once, edit captions/alt text,
  collection, order and visibility, delete records and their uploaded files.
- **Juice bar items** — CRUD for the homepage product cards, filtering data and
  ordering.
- **Action toasts** — every save, upload, visibility change and delete confirms
  with an in-panel toast.
- **Seed content** — the existing 9 menu sheets, 89 gallery photographs, 11 juice
  bar cards and all current site copy are imported by the seeders.

## Requirements

- PHP 8.2+ with `gd` (WebP support), `pdo_sqlite` or `pdo_mysql`, `mbstring`,
  `xml`, `curl` and `zip`.
- Composer 2.
- SQLite or MySQL/MariaDB. Both are supported; the current production deployment uses SQLite.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000` and the admin at
`http://127.0.0.1:8000/admin/login`.

The admin account is created from `ADMIN_NAME`, `ADMIN_EMAIL` and
`ADMIN_PASSWORD` in `.env` the first time `AdminUserSeeder` runs. Change these
values before seeding a new environment. The seeder does not overwrite an
existing account or password.

If you change `ADMIN_PASSWORD` after the user exists and want to reset it, use
`php artisan tinker` or create a dedicated password command; the site does not
provide public password reset.

## Admin routes

| Route | Purpose |
| --- | --- |
| `/admin` | Overview, counts and recent photographs |
| `/admin/two-factor` | Authenticator enrolment / 6-digit challenge |
| `/admin/content` | All public copy, brand, SEO, contact and image settings |
| `/admin/menu` | Kitchen menu sheets |
| `/admin/gallery` | Gallery records and batch uploads |
| `/admin/products` | Juice bar cards |

### Admin two-step verification

The first successful password sign-in shows a QR code and setup key. Scan it
with Google Authenticator, Microsoft Authenticator, Authy, 1Password or any TOTP
app, then confirm one 6-digit code. Later sign-ins require the current
authenticator code. The code input auto-submits at six digits, and the
**Verify and sign in** button submits manually.

If the authenticator is lost, clear the enrolled secret from the server and sign
in again to re-enrol:

```bash
php artisan tinker --execute="\$u = App\Models\User::where('email', 'ADMIN_EMAIL')->first(); \$u->forceFill(['two_factor_secret' => null, 'two_factor_confirmed_at' => null])->save();"
```

`APP_KEY` must stay stable after enrolment because the 2FA secret is encrypted
with it.

Uploads are written to `public/uploads/` and served directly. No
`php artisan storage:link` is needed.

## Image pipeline

`App\Services\ImageService` reads JPG, PNG, WebP, AVIF, GIF or BMP uploads with
GD and writes a compressed WebP:

- content/brand images: max 2200px, quality 84
- gallery: max 1800px, quality 84
- menu: max 1600px, quality 86
- juice bar: max 1400px, quality 84

EXIF orientation is applied before resizing. Replacing an uploaded image deletes
the previous uploaded file; seeded files under `public/assets/` are never
deleted.

`public/.user.ini` raises PHP's upload limits for FPM/Apache deployments
(12 MB per file, 64 MB per request). Confirm the host honours `.user.ini`, or set
the same values in the server's PHP-FPM pool. The CLI server ignores
`.user.ini`; use `php -d upload_max_filesize=12M -d post_max_size=64M artisan serve`
if you need to test large uploads locally.

## Content architecture

- `app/Support/SiteContent.php` defines the editable groups, field types and
  defaults. `settings` stores key/value overrides.
- `app/Models/MenuPage.php`, `GalleryPhoto.php` and `Product.php` are the
  record models used by both the public site and Livewire admin components.
- Public controllers live in `app/Http/Controllers/PageController.php`; admin
  components live in `app/Livewire/Admin/`.
- Public views are in `resources/views/site/`; the admin layout and Livewire
  views are in `resources/views/layouts/admin.blade.php` and
  `resources/views/livewire/admin/`.
- CSS/JS assets are in `public/assets/` and remain the canonical frontend files.

The original standalone `code.html`, `gallery.html` and `menu.html` are retained
at the project root as design reference only. Laravel serves the Blade versions
and does not route those files.

## Live deployment

The current production site is **https://awais.cc/sipandsmoke** with the admin at
**https://awais.cc/sipandsmoke/admin/login**. It uses SQLite at
`database/database.sqlite` and is already migrated and seeded. The app supports
being served from a subdirectory: set `APP_URL`/`ASSET_URL` to the public URL and
Livewire routes are prefixed automatically from the URL path.

## Production notes

1. Set `APP_ENV=production`, `APP_DEBUG=false`, the real `APP_URL` and the
   database settings in `.env` (absolute SQLite path, or MySQL credentials).
2. Point the web server document root at `public/` (not the project root).
3. Make `storage/`, `bootstrap/cache/` and (for SQLite) `database/` writable by
   the web user.
4. Run `php artisan migrate --force` and seed once with
   `php artisan db:seed --force`.
5. Cache configuration/routes/views if desired:
   `php artisan optimize`.
6. Keep `public/uploads/` on persistent storage and include it in backups.
7. Serve over HTTPS so the admin session cookie is secure.

For a first deploy, seed before exposing the admin account. The seeded demo
content can be replaced entirely from `/admin/content`.

For a step-by-step Bluehost deployment, including cPanel, SQLite and MySQL,
seeding, no-SSH and document-root options, see
[`BLUEHOST_DEPLOYMENT.md`](BLUEHOST_DEPLOYMENT.md).
