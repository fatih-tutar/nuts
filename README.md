# Nuts

Private kuruyemiş (nuts) sales ledger. Login-only; not indexed.

- Domain (planned): `https://nuts.karesimakine.com`
- Railway demo: `https://web-production-06f69b0c.up.railway.app`
- Stack: Laravel 13 + Breeze (Blade) + PostgreSQL

## Login (seeded on deploy)

- Email: `admin@nuts.test`
- Password: `password`

Registration is disabled. Change the password after first login.

## Local (Docker)

Ports avoid clash with karesi-machine (`8090`/`8091`/`5433`):

| Service | URL / port |
|---------|------------|
| App | http://localhost:8092 |
| pgAdmin | http://localhost:8093 |
| Postgres (host) | `localhost:5434` |

```bash
cp .env.example .env   # if needed; Docker DB settings are already there
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker run --rm -v "$PWD":/app -w /app node:22-bookworm bash -c "npm install && npm run build"
```

Login: `admin@nuts.test` / `password`
