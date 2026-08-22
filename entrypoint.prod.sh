#!/bin/bash
set -e

log() { echo "[entrypoint] $*"; }

# ── 1. Tunggu PostgreSQL siap ────────────────────────────────────────────────
log "Menunggu database siap ..."
until php -r "
  \$pdo = new PDO(
    'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
  );
" 2>/dev/null; do
  sleep 2
done
log "Database siap."

# ── 2. Jalankan migrasi ──────────────────────────────────────────────────────
# Restore data awal (kalau ada) sudah ditangani otomatis oleh Postgres lewat
# docker-entrypoint-initdb.d (lihat docker/initdb/ + docker-compose.prod.yml) —
# hanya jalan sekali saat volume postgres_data masih kosong. Di sini cukup
# migrate seperti biasa; kalau restore sudah jalan, ini cuma sinkron migration
# yang belum ada di dump (biasanya no-op).
log "Menjalankan migrasi ..."
php artisan migrate --force --no-interaction

# ── 3. Pastikan symlink public/storage -> storage/app/public ────────────────
log "Memastikan symlink public/storage ..."
php artisan storage:link || true

# ── 4. Cache config/route/view untuk performa production ────────────────────
# TIDAK ada AdminSeeder di sini (beda dari entrypoint.sh dev) — seeder itu
# me-reset password admin ke "admin" setiap container start, fatal di production.
# Lihat DEPLOYMENT.md Bagian 8 untuk detail temuannya.
log "Cache config/route/view ..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 5. Start server ───────────────────────────────────────────────────────────
log "Menjalankan Laravel di 0.0.0.0:8000 ..."
exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
