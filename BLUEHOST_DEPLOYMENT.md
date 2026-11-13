# Bluehost deployment guide — Sip & Smoke

This is the step-by-step guide for putting the Laravel 12 + Livewire 3 site live on
Bluehost. Follow the quick start first; the sections below explain each step in detail.

The public HTML files in the repository root (`code.html`, `menu.html`,
`gallery.html`) are legacy design references. The deployed application is the
Laravel app served from `public/`.

---

## Quick start — 12 steps

You only need **cPanel**, the project ZIP, and the database details you create in step 2.

1. **Set PHP 8.2 or 8.3** in cPanel → *Select PHP Version*. Enable `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` (with WebP), `zip`, `curl`, `xml`, `bcmath` and `exif`.
2. **Create the database** in cPanel → *MySQL Databases*. Create a database and user, then give the user **All Privileges**. Note the full database name, username and password.
3. **Build the ZIP on your computer** from the project folder:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan optimize:clear
   ```
   Include `app/ bootstrap/ config/ database/ public/ resources/ routes/ storage/ vendor/ artisan composer.json composer.lock .env.example`. Exclude `.env`, `node_modules/`, `.git/`, `tests/`, `Photos/`, `*.zip`, `*.pdf` and other large files.
4. **Upload and extract** the ZIP into `/home/USERNAME/sipandsmoke` using cPanel → *File Manager*. The `artisan` file must be directly inside that folder.
5. **Point the domain to `public/`**: cPanel → *Domains* → *Manage* → set **Document Root** to `/home/USERNAME/sipandsmoke/public`.
6. **Create `.env`**: enable *Show Hidden Files*, copy `.env.example` to `.env`, then fill in `APP_URL`, database details and `ADMIN_*` values. Set `APP_ENV=production` and `APP_DEBUG=false`.
7. **Generate the app key once** from cPanel *Terminal* (or SSH):
   ```bash
   cd ~/sipandsmoke
   php artisan key:generate --force
   ```
   Save the generated `APP_KEY` somewhere safe. Never regenerate it after 2FA is set up.
8. **Create the tables and first admin**:
   ```bash
   php artisan migrate --force --seed
   ```
9. **Finish the first deploy**:
   ```bash
   php artisan optimize
   chmod -R 775 storage bootstrap/cache public/uploads
   ```
10. **Open the site and admin login**: visit `https://yourdomain.com` and `https://yourdomain.com/admin/login`. Sign in with the `ADMIN_EMAIL` and `ADMIN_PASSWORD` from `.env`, then scan the 2FA QR code and confirm the 6-digit code.
11. **Change the admin email/password from the panel**: go to **Admin → Account**. Enter the current password, set the new email and password, then save. Next sign-in uses the new details.
12. **Back up** the database, `.env` (especially `APP_KEY`) and `public/uploads/` before making future changes.

> No SSH or Terminal on your plan? Use the one-time Cron Job method in section 8.

---

## 1. What was added for admin 2FA

| Item | Detail |
| --- | --- |
| TOTP library | `pragmarx/google2fa` v9 |
| QR rendering | `bacon/bacon-qr-code` v3 |
| Database | `users.two_factor_secret`, `users.two_factor_confirmed_at` |
| Encryption | The TOTP secret is stored with Laravel's `encrypted` cast |
| Login flow | Password → 6-digit authenticator code → admin session |
| First login | The admin is shown a QR code and setup key, then confirms one code |
| Later logins | The admin enters the current 6-digit code |
| UX | Brand-coloured verification screen, animated 6-cell code input, auto-submit at 6 digits, plus a visible submit button |
| Routes | `/admin/two-factor` (GET/POST), `/admin/two-factor/cancel` (POST) |

The password step no longer creates an authenticated session. Until the
authenticator code is accepted, the visitor is only a “pending” sign-in stored
in the session for 10 minutes.

> **Important:** never change `APP_KEY` after the admin has enrolled. The 2FA
> secret is encrypted with it. Keep a copy of the production `APP_KEY` in the
> site owner's password manager.

---

## 2. Recommended Bluehost setup

Use **Bluehost shared hosting with cPanel + SSH/Terminal** if it is available on
the plan. It is the quickest and least error-prone option.

Target layout:

```text
/home/USERNAME/sipandsmoke/        ← Laravel application
/home/USERNAME/sipandsmoke/public  ← web document root
```

Use a real MySQL database. Do **not** use SQLite in production.
Set the PHP version to **8.2 or 8.3**.

### Bluehost requirements

Enable or confirm these in **cPanel → Select PHP Version → Extensions**:

- `gd` with WebP support
- `pdo_mysql`, `mysqli`
- `mbstring`, `xml`, `curl`, `zip`, `openssl`, `fileinfo`
- `tokenizer`, `ctype`, `json`, `bcmath`, `exif`

In **MultiPHP INI Editor**, set at least:

```ini
memory_limit = 256M
upload_max_filesize = 32M
post_max_size = 40M
max_execution_time = 120
max_input_vars = 3000
```

If WebP is missing from GD, image uploads from the admin will fail. Ask
Bluehost support to enable WebP for the selected PHP version.

---

## 3. Build the deployment package (on your computer)

Run these from the project root:

```bash
composer install --no-dev --optimize-autoloader

# Clear local caches before packaging
php artisan optimize:clear
```

Create a ZIP containing the application, but exclude local-only and oversized
files:

```text
Include:
  app/  bootstrap/  config/  database/  public/  resources/  routes/
  storage/  vendor/  artisan  composer.json  composer.lock  .env.example

Exclude:
  node_modules/  .git/  tests/  Photos/  Photos.zip  Logo.zip
  menu.pdf  screen.png  Unconfirmed*.crdownload
  stitch_*/  *.zip  .env  database/database.sqlite
```

Including `vendor/` makes the upload larger but removes the need for Composer
on Bluehost. If your upload limit is lower than the ZIP size, upload over FTP
or use the SSH route in section 4B.

---

## 4A. Deploy with cPanel File Manager (no SSH)

1. **Create the database**
   - cPanel → **MySQL Databases**
   - Create database: `USERNAME_sipandsmoke`
   - Create user and a strong password
   - Add the user to the database with **All Privileges**
   - Write down the full database name, username and password.

2. **Upload the app**
   - cPanel → **File Manager**
   - Create `/home/USERNAME/sipandsmoke`
   - Upload the ZIP there and extract it
   - The `artisan` file must sit directly inside `/home/USERNAME/sipandsmoke`
   - `vendor/` must sit beside it

3. **Point the domain at `public/`**
   - cPanel → **Domains**
   - Open the domain/subdomain → **Manage**
   - Set **Document Root** to `/home/USERNAME/sipandsmoke/public`
   - If the primary domain cannot be changed, create a subdomain such as
     `app.yourdomain.com` and use the same document root.

4. **Create `.env`**
   - In File Manager, enable **Show Hidden Files**
   - Copy `.env.example` to `.env`
   - Edit it using the values below.

5. **Run the first migration**
   - If cPanel **Terminal** is available, run section 5.
   - If not, use the one-time Cron Job method in section 8.

6. **Set permissions**
   - In File Manager, set `storage`, `bootstrap/cache` and `public/uploads` to
     `775` recursively. Files should be `644`, directories `755`.

---

## 4B. Deploy with SSH / cPanel Terminal (recommended)

```bash
# 1. Connect
ssh USERNAME@your-server -p 2222

# 2. Application folder
mkdir -p ~/sipandsmoke
cd ~/sipandsmoke

# 3. Upload the project here first (FTP/SFTP/Git), then:
composer install --no-dev --optimize-autoloader

# 4. Environment
cp .env.example .env
nano .env
```

If Composer is not installed on the server:

```bash
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=$HOME/bin --filename=composer
rm composer-setup.php
export PATH="$HOME/bin:$PATH"
```

Then run the deploy commands in section 5.

---

## 5. Production `.env`

Replace every placeholder:

```dotenv
APP_NAME="Sip & Smoke"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=USERNAME_sipandsmoke
DB_USERNAME=USERNAME_sipandsmoke
DB_PASSWORD=THE_STRONG_PASSWORD

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=sync

ADMIN_NAME="Sip & Smoke Admin"
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD="a-long-unique-password"
```

Generate the application key **once**:

```bash
php artisan key:generate --force
```

Copy the generated `APP_KEY` to a safe place immediately. Do not regenerate it
on later deployments.

---

## 6. First-deploy commands

Run from `/home/USERNAME/sipandsmoke`:

```bash
php artisan config:clear
php artisan migrate --force --seed
php artisan optimize

chmod -R 775 storage bootstrap/cache public/uploads
```

Then:

- Visit `https://yourdomain.com`
- Visit `https://yourdomain.com/admin/login`
- Sign in with `ADMIN_EMAIL` / `ADMIN_PASSWORD`

The first admin sign-in opens the **Scan to secure** screen. Scan the QR code
with Google Authenticator, Microsoft Authenticator, Authy, 1Password or any
TOTP-compatible app, then enter the 6-digit code. The code field auto-submits
when the sixth digit is typed; the **Confirm and sign in** button also works.

From the next login onwards, the admin uses email + password + the current
authenticator code.

---

## 7. Subsequent deployments

Back up the database and `public/uploads` first, then:

```bash
cd ~/sipandsmoke
php artisan down

# replace application files, keeping:
#   .env
#   storage/
#   public/uploads/

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
chmod -R 775 storage bootstrap/cache public/uploads

php artisan up
```

Do not run `--seed` again on a live database unless you intentionally want the
seed data recreated. The seeders use `firstOrCreate`, but it is unnecessary on
routine deployments.

---

## 8. No-SSH fallback: run commands with a one-time Cron Job

Use this only if cPanel has no Terminal or SSH.

1. cPanel → **Cron Jobs**
2. Add a one-time job (minute/hour just ahead of the current server time):

```bash
/usr/local/bin/php /home/USERNAME/sipandsmoke/artisan migrate --force --seed >> /home/USERNAME/sipandsmoke/storage/logs/deploy.log 2>&1
```

3. Wait for it to run, then check
   `/home/USERNAME/sipandsmoke/storage/logs/deploy.log`.
4. Add a second one-time job for caching:

```bash
/usr/local/bin/php /home/USERNAME/sipandsmoke/artisan optimize >> /home/USERNAME/sipandsmoke/storage/logs/deploy.log 2>&1
```

5. **Delete both cron jobs after they run.**

If `/usr/local/bin/php` is not correct, use the PHP binary shown in cPanel or
ask Bluehost support for the CLI path for the selected PHP version.

---

## 9. Document root fallback (only if `public/` cannot be the root)

Bluehost normally permits changing the document root for addon domains and
subdomains. If the primary domain is locked to `public_html`, use this fallback:

```text
/home/USERNAME/sipandsmoke/        ← Laravel app
/home/USERNAME/public_html/        ← copied contents of public/
```

1. Back up the existing `public_html`.
2. Copy everything from `sipandsmoke/public/` into `public_html/`.
3. Edit `public_html/index.php` and point the two paths at the app folder:

```php
require __DIR__.'/../sipandsmoke/vendor/autoload.php';

$app = require_once __DIR__.'/../sipandsmoke/bootstrap/app.php';
```

4. Copy `.htaccess`, `assets/`, `favicon.ico` and `robots.txt` with the rest of
   `public/`.
5. Make the uploads visible at the web root:

```bash
ln -s /home/USERNAME/sipandsmoke/public/uploads /home/USERNAME/public_html/uploads
```

If the host disallows symlinks, the cleaner solution is to use a subdomain with
its document root set to `sipandsmoke/public`. Uploaded images are written to
the app's `public/uploads`, so the document-root method is strongly preferred.

---

## 10. HTTPS, security and permissions

After the domain resolves and AutoSSL has issued a certificate:

1. cPanel → **SSL/TLS Status** → run AutoSSL if needed.
2. Force HTTPS by adding this at the top of `public/.htaccess` (or
   `public_html/.htaccess` in the fallback layout):

```apache
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

3. Confirm `APP_DEBUG=false` and `APP_ENV=production`.
4. Keep `.env` outside the web root and never commit it.
5. Only `public/` should be web-accessible. `app/`, `config/`, `database/`,
   `storage/` and `vendor/` must not be reachable by URL.
6. Back up:
   - the MySQL database
   - `.env` / `APP_KEY`
   - `public/uploads/`
   - the last known-good application folder

Recommended permissions:

```bash
find /home/USERNAME/sipandsmoke -type d -exec chmod 755 {} \;
find /home/USERNAME/sipandsmoke -type f -exec chmod 644 {} \;
chmod -R 775 /home/USERNAME/sipandsmoke/storage
chmod -R 775 /home/USERNAME/sipandsmoke/bootstrap/cache
chmod -R 775 /home/USERNAME/sipandsmoke/public/uploads
```

---

## 11. Recovering a lost authenticator

If the admin loses the phone/authenticator:

1. Sign in to cPanel → **Terminal** (or use SSH).
2. Run:

```bash
cd ~/sipandsmoke

php artisan tinker --execute="\$u = App\Models\User::where('email', 'ADMIN_EMAIL_HERE')->first(); \$u->forceFill(['two_factor_secret' => null, 'two_factor_confirmed_at' => null])->save();"
```

3. Sign in again at `/admin/login`. The next login will show the QR setup screen
   and allow enrolling a new authenticator.

If `APP_KEY` was changed or lost, the encrypted secret cannot be decrypted.
Use the same reset command, then keep the new `APP_KEY` backed up.

---

## 12. Troubleshooting

| Symptom | Likely cause | Fix |
| --- | --- | --- |
| 500 error after upload | `.env` missing or `APP_KEY` blank | Create `.env`, run `php artisan key:generate --force` |
| “No application encryption key” | `APP_KEY` empty | Generate and save the key |
| Database connection error | Wrong DB name/user/password or MySQL not selected | Recheck cPanel MySQL values and `DB_*` in `.env` |
| 2FA QR does not appear | First sign-in not reached, session issue, or missing `APP_KEY` | Clear caches, confirm `SESSION_DRIVER=database`, check `storage/logs/laravel.log` |
| Code always rejected | Server clock is wrong | Sync the server clock or ask Bluehost support; TOTP is time-based |
| Admin locked out | Authenticator device lost | Reset 2FA with the tinker command in section 11 |
| Uploaded images 404 | Document root is `public_html`, not `public/` | Use the document-root method or symlink `uploads` |
| Image upload fails | GD/WebP extension missing or upload limits too low | Enable `gd` with WebP and raise PHP upload limits |
| CSS/JS missing | `public/assets` not uploaded or wrong document root | Re-upload `public/` and verify the root path |
| White page with no error | `APP_DEBUG=false` hides the exception | Check `storage/logs/laravel.log`; temporarily set `APP_DEBUG=true` only while diagnosing |
| 419 page expired | Session/cookie mismatch or `APP_URL`/HTTPS mismatch | Clear config cache, confirm `APP_URL` uses `https://`, keep `SESSION_SECURE_COOKIE=true` |

Useful diagnostics:

```bash
php artisan about
php artisan route:list
php artisan migrate:status
tail -n 100 storage/logs/laravel.log
```

---

## 13. Go-live checklist

- [ ] PHP 8.2/8.3 selected
- [ ] GD with WebP, PDO MySQL and required extensions enabled
- [ ] MySQL database and user created
- [ ] Application uploaded to `/home/USERNAME/sipandsmoke`
- [ ] Document root set to `/home/USERNAME/sipandsmoke/public`
- [ ] `.env` created with production values
- [ ] `APP_KEY` generated once and backed up
- [ ] `APP_DEBUG=false`
- [ ] `php artisan migrate --force --seed` completed
- [ ] `storage`, `bootstrap/cache`, `public/uploads` writable
- [ ] HTTPS active and forced
- [ ] Public site loads
- [ ] Admin login loads
- [ ] Admin 2FA QR enrollment completed
- [ ] Admin email/password changed from **Admin → Account**
- [ ] Logout and log back in with an authenticator code
- [ ] Database, `.env` and uploads backed up
- [ ] One-time migration cron jobs removed, if used
