# FCPR SynergyPortal

A monorepo containing two separate Laravel applications for managing web hosting services resold via the [Synergy Wholesale](https://synergywholesale.com) platform.

---

## Applications

### Customer Portal (`/customer-portal`)

A self-service portal for customers to manage their hosting services.

- Self-registration with email verification
- View and manage cPanel hosting accounts
- Domain registration and management (nameservers, auto-renew, lock/unlock)
- SSL certificate tracking
- DNS record management (A, AAAA, CNAME, MX, TXT, NS, SRV)
- Invoice history with GST breakdown
- Account profile management

URL: `https://portal.yourdomain.com`

### Admin Portal (`/admin-portal`)

An internal management portal for the business owner to provision and manage all customer services.

- Login-only — no public registration (seeded admin account)
- Full customer management (create, view, toggle active status)
- Provision and manage hosting accounts via Synergy Wholesale API
- Domain registration, renewal, nameserver updates, and transfers
- SSL certificate provisioning
- DNS record management across all customer domains
- Invoice creation with automatic 10% GST calculation and line items
- Synergy API status dashboard with account balance and domain sync

URL: `https://admin.yourdomain.com`

---

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 11 (PHP 8.2+) |
| Templating | Blade (server-rendered) |
| Authentication | Laravel Breeze |
| Styling | Tailwind CSS via Vite |
| Database | MySQL (production) / SQLite (development) |
| Upstream API | Synergy Wholesale SOAP API v3.16 |
| API Package | `hampel/synergy-wholesale` (pure SOAP wrapper) |
| Hosting | cPanel shared hosting (Apache + .htaccess) |

Both apps share a single database. The admin portal provisions services; the customer portal reads and displays them.

---

## Project Structure

```
FCPR_SynergyPortal/
├── customer-portal/    # Laravel app — customer self-service
└── admin-portal/       # Laravel app — internal management
```

Each app is a fully independent Laravel installation with its own `composer.json`, `.env`, migrations, routes, controllers, and views.

---

## Development Setup

### Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- SQLite (for local development)

### Initial setup

```bash
# Customer portal
cd customer-portal
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev

# Admin portal (separate terminal)
cd admin-portal
composer install
cp .env.example .env
php artisan key:generate
# Point DB_DATABASE in .env to the customer-portal SQLite file, or use a shared MySQL DB
php artisan db:seed --class=AdminSeeder
npm install && npm run dev
```

### Synergy Wholesale credentials

Add your credentials to both `.env` files:

```env
SYNERGY_RESELLER_ID=your_reseller_id
SYNERGY_API_KEY=your_api_key
```

Credentials are available in your Synergy Wholesale reseller account. Your server's IP must be whitelisted in the Synergy portal for API calls to succeed.

Test the connection via Tinker:

```bash
php artisan tinker
app(\App\Services\SynergyWholesaleService::class)->testConnection();
```

---

## Deploying to cPanel Shared Hosting

### 1. Build assets locally

Run this on your local machine before uploading — do not run Vite on the server.

```bash
cd customer-portal && npm install && npm run build
cd ../admin-portal && npm install && npm run build
```

### 2. Create subdomains in cPanel

In cPanel → **Subdomains**, create two entries and set document roots manually:

| Subdomain | Document Root |
|---|---|
| `portal.yourdomain.com` | `public_html/customer-portal/public` |
| `admin.yourdomain.com` | `public_html/admin-portal/public` |

### 3. Upload files

Upload the entire repo to `public_html/` via File Manager or FTP. Include the `vendor/` directories.

If you have SSH access, you can run Composer on the server instead:

```bash
composer install --no-dev --optimize-autoloader
```

### 4. Configure production `.env`

In `customer-portal/.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://portal.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_db_password

SYNERGY_RESELLER_ID=your_id
SYNERGY_API_KEY=your_key

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Business Name"
```

Repeat for `admin-portal/.env`, pointing `APP_URL` to the admin subdomain and using the **same database credentials**.

### 5. Run migrations (requires SSH)

```bash
cd ~/public_html/customer-portal && php artisan migrate --force
cd ~/public_html/admin-portal && php artisan db:seed --class=AdminSeeder --force
```

If you don't have SSH, run `php artisan migrate` locally against a local MySQL instance, export the schema via phpMyAdmin, and import it on the server. Then manually insert the admin user.

### 6. Set storage permissions

```bash
chmod -R 755 ~/public_html/customer-portal
chmod -R 775 ~/public_html/customer-portal/storage
chmod -R 775 ~/public_html/customer-portal/bootstrap/cache

chmod -R 755 ~/public_html/admin-portal
chmod -R 775 ~/public_html/admin-portal/storage
chmod -R 775 ~/public_html/admin-portal/bootstrap/cache
```

### 7. Cache configuration

```bash
cd ~/public_html/customer-portal
php artisan config:cache && php artisan route:cache && php artisan view:cache

cd ~/public_html/admin-portal
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### 8. Set up cron jobs

In cPanel → **Cron Jobs**, add one entry per app set to run every minute:

```
* * * * * /usr/local/bin/php /home/yourusername/public_html/customer-portal/artisan schedule:run >> /dev/null 2>&1
* * * * * /usr/local/bin/php /home/yourusername/public_html/admin-portal/artisan schedule:run >> /dev/null 2>&1
```

Replace `yourusername` with your actual cPanel username.

### Notes

- The `.htaccess` file inside each app's `public/` directory is required for Laravel routing to work on Apache. It is included in the repo — ensure it is not excluded by your FTP client (some hide dotfiles by default).
- `APP_DEBUG` must be `false` in production to prevent sensitive information being exposed in error pages.
- Set a strong, unique `APP_KEY` in each app's production `.env` — generated via `php artisan key:generate`.

---

## Admin Account

The admin portal has no public registration. The initial admin account is created via seeder.

Set these in `admin-portal/.env` before seeding:

```env
ADMIN_SEED_EMAIL=you@yourdomain.com
ADMIN_SEED_PASSWORD=YourSecurePassword
```

Then run:

```bash
php artisan db:seed --class=AdminSeeder
```

The seeder uses `updateOrCreate`, so it is safe to re-run to update credentials.
