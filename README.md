# Nuts

Private kuruyemiş (nuts) sales ledger. Login-only; not indexed.

- Domain (planned): `https://nuts.karesimakine.com`
- Railway demo: `https://web-production-06f69b0c.up.railway.app`
- Stack: Laravel 13 + Breeze (Blade) + PostgreSQL

## Login (seeded on deploy)

- Email: `admin@nuts.test`
- Password: `password`

Registration is disabled. Change the password after first login.

## Local

```bash
cp .env.example .env
# set DB_CONNECTION=pgsql and DB_* or use sqlite for quick try
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```
