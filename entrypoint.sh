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

# ── 2. Jalankan migrasi baru (jika ada) ─────────────────────────────────────
log "Menjalankan migrasi ..."
php artisan migrate --force --no-interaction 2>&1 | tail -5

# ── 3. Pastikan symlink public/storage -> storage/app/public ────────────────
php artisan storage:link || true

# ── 4. Reset password admin (dev only) ──────────────────────────────────────
log "Reset password admin ..."
php artisan db:seed --class=AdminSeeder --force --no-interaction

# ── 5. Bersihkan cache config ────────────────────────────────────────────────
php artisan config:clear 2>/dev/null || true

# ── 6. Start server ──────────────────────────────────────────────────────────
log "Menjalankan Laravel server di 0.0.0.0:8000 ..."
# --no-reload wajib: tanpa ini, `artisan serve` memfilter env var yang diteruskan
# ke proses yang menangani request (hanya whitelist APP_ENV/PATH/dst yang lolos),
# sehingga DB_HOST=postgres dari docker-compose tidak sampai dan fallback ke .env (127.0.0.1).
exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
