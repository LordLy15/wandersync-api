# WanderSync - Full Railway Deployment Guide

## Prerequisites
- Akun Railway (https://railway.app)
- Project Supabase sudah aktif
- GitHub repository terhubung

---

## Step 1: Environment Variables di Railway

Buka Railway Dashboard → Project → Variables → Tambahkan:

```env
# App (generate APP_KEY dengan: php artisan key:generate --show)
APP_NAME=WanderSync
APP_ENV=production
APP_KEY=YOUR_GENERATED_APP_KEY
APP_DEBUG=false
APP_URL=https://wandersync-api.up.railway.app

# Database (Supabase - dari Settings > Database)
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YOUR_SUPABASE_PASSWORD
DB_SSLMODE=require

# Session & Queue
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info

# Supabase (dari Settings > API)
SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co
SUPABASE_ANON_KEY=YOUR_ANON_KEY
SUPABASE_SERVICE_ROLE_KEY=YOUR_SERVICE_ROLE_KEY

# Google OAuth (isi kalau ada)
# GOOGLE_CLIENT_ID=your-client-id
# GOOGLE_CLIENT_SECRET=your-client-secret
# GOOGLE_REDIRECT_URI=https://wandersync-api.up.railway.app/auth/google/callback
```

---

## Step 2: Generate APP_KEY

Lokal:
```bash
php artisan key:generate --show
```

Copy output dan paste ke variable `APP_KEY` di Railway.

---

## Step 3: Files yang harus ada

```
wandersync-api/
├── Dockerfile
├── Procfile (web: ./start.sh)
├── start.sh
├── router.php
├── composer.json
├── composer.lock
└── .dockerignore
```

---

## Step 4: Deploy

1. Push ke GitHub
2. Railway auto-deploy dari branch `main`
3. Tunggu sampai status "Deployed" (hijau)

---

## Verifikasi

Test API:
```
https://wandersync-api.up.railway.app/api/trips
```

Response yang benar:
```json
{"message":"Unauthenticated."}
```

(Ini berarti API jalan, hanya butuh authentication)

---

## Troubleshooting

### Error: "Unsupported operand types: string + int"
Pastikan `start.sh` handle PORT dengan benar

### Error: 404 Not Found
Pastikan `router.php` ada dan executable

### Error: Database Connection Failed
Cek:
1. DB credentials benar
2. Supabase project aktif
3. Connection pooling enabled di Supabase

### Error: APP_KEY not set
Generate dengan: `php artisan key:generate --show`
