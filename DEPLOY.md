# WanderSync API - Deployment Guide

## Local Development

```bash
# Install dependencies
composer install

# Run migrations (SQLite for local)
php artisan migrate

# Start server
php artisan serve
```

## Railway Deployment

### 1. Prerequisites
- Akun Railway (https://railway.app)
- Project Supabase sudah aktif

### 2. Connect Repository
1. Buka https://railway.app
2. Klik "New Project" → "Deploy from GitHub repo"
3. Connect repository `wandersync-api`

### 3. Setup Environment Variables

Di Railway Dashboard → Project → Variables, tambahkan:

```
APP_NAME=WanderSync
APP_ENV=production
APP_KEY=<generate baru>
APP_DEBUG=false
APP_URL=https://wandersync-api.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=<password Supabase>
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

LOG_CHANNEL=stack
LOG_LEVEL=info

# Supabase
SUPABASE_URL=https://belfgdknfqsmwfagswkt.supabase.co
SUPABASE_ANON_KEY=<anon key dari Supabase>
SUPABASE_SERVICE_ROLE_KEY=<service role key dari Supabase>

# Google OAuth (isi sesuai konfigurasi)
GOOGLE_CLIENT_ID=<client ID>
GOOGLE_CLIENT_SECRET=<client secret>
GOOGLE_REDIRECT_URI=https://wandersync-api.up.railway.app/auth/google/callback
```

### 4. Generate APP_KEY

```bash
php artisan key:generate --show
```

Copy output ke variable `APP_KEY` di Railway.

### 5. Deploy
1. Railway auto-deploy dari branch `main`
2. Atau klik "Deploy" manually

### 6. Setup Supabase Connection (jika belum bisa)

Jika deployment gagal karena koneksi database:

1. Buka Supabase Dashboard
2. Settings → Database
3. Di "Connection Pooling" → aktifkan
4. Copy connection string URI
5. Paste ke Railway sebagai `DATABASE_URL`

### 7. Verifikasi

```bash
# Test API
curl https://wandersync-api.up.railway.app/api/trips
```

---

## Database Migration

Untuk production, migration sudah jalan otomatis saat deploy. Jika perlu manual:

1. Railway Shell → `php artisan migrate`
2. Atau setup via Supabase SQL Editor langsung
