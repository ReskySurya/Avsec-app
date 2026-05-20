# AVSEC - Deployment Runbook

Production deployment menggunakan Docker (3 container: app + queue + scheduler)
dengan **MySQL native di host** dan **nginx di luar container** (host).

Project di-deploy di **`/upt/apps`**.

---

## 1. Arsitektur

```
                  Internet (HTTPS)
                       │
                       ▼
              ┌────────────────────┐
              │   HOST nginx       │  TLS terminate, static serve
              │   (Let's Encrypt)  │
              └────────┬───────────┘
                       │ fastcgi_pass 127.0.0.1:9000
                       ▼
              ┌────────────────────┐
              │  CONTAINER: app    │  php-fpm + Chromium + Puppeteer
              │  (php-fpm:9000)    │
              └────────┬───────────┘
                       │ host.docker.internal -> 172.17.0.1:3306
                       ▼
              ┌────────────────────┐
              │  HOST native MySQL │  bind 172.17.0.1, ufw allow docker bridge
              └────────────────────┘

  Container lain:
    - queue       : artisan queue:work --tries=3
    - scheduler   : cron -> artisan schedule:run

  Bind mounts (HOST <-> CONTAINER):
    /upt/apps/storage       <->  /var/www/html/storage     (uploads, logs)
    /upt/apps/public        <->  /var/www/html/public      (static, build)
    /upt/apps/docker/scheduler/crontab <-> /etc/cron.d/avsec (read-only)
```

**Tidak ada container database, tidak ada named volume.** Semua data
persisten ada di filesystem host (`/upt/apps/storage` dan MySQL native
di `/var/lib/mysql`).

---

## 2. Persyaratan Server

| Komponen | Versi minimum | Catatan |
|---|---|---|
| OS | Debian 12 / Ubuntu 22.04+ | atau RHEL 9+ |
| Docker Engine | 24.x | `apt install docker-ce docker-compose-plugin` |
| Nginx | 1.22 | host nginx |
| MySQL Server | 8.0 | native, bukan container |
| Node.js | 20.x | hanya untuk build asset Vite |
| RAM | >= 1.5 GB | hemat karena DB native |
| Disk | >= 15 GB | image + storage growth |
| Certbot | latest | untuk Let's Encrypt |

---

## 3. Persiapan Awal (sekali per server)

### 3.1. Install Docker, Node, Nginx, MySQL

```bash
# Docker (ikuti https://docs.docker.com/engine/install/)
sudo apt update
sudo apt install -y docker.io docker-compose-plugin nginx mysql-server certbot python3-certbot-nginx

# Node 20 (untuk build asset)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verifikasi
docker --version
docker compose version
node -v
nginx -v
mysql --version
```

### 3.2. Setup MySQL native

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf` dan bind ke docker bridge gateway:

```ini
[mysqld]
bind-address           = 172.17.0.1
mysqlx-bind-address    = 127.0.0.1
character-set-server   = utf8mb4
collation-server       = utf8mb4_unicode_ci
default-time-zone      = '+07:00'
```

> **Penting**: jangan bind ke `0.0.0.0` kecuali firewall sudah ketat.
> Bind `172.17.0.1` membatasi akses hanya dari container Docker.

Restart dan buat database + user:

```bash
sudo systemctl restart mysql
sudo systemctl enable mysql

sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS avsec
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'avsec'@'172.%.%.%' IDENTIFIED BY 'GANTI_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON avsec.* TO 'avsec'@'172.%.%.%';
FLUSH PRIVILEGES;
SQL
```

Firewall (ufw):

```bash
sudo ufw allow from 172.17.0.0/16 to any port 3306 proto tcp
sudo ufw status
```

Verifikasi bisa konek dari container:

```bash
# Setelah project ter-deploy, test:
docker compose run --rm app sh -c \
    'mysql -h host.docker.internal -uavsec -p"$DB_PASSWORD" -e "SHOW DATABASES;"'
```

### 3.3. Setup direktori project

```bash
sudo mkdir -p /upt/apps
sudo chown $USER:$USER /upt/apps

# Clone repo
git clone <REPO_URL> /upt/apps
cd /upt/apps
```

### 3.4. Konfigurasi environment

```bash
cp .env.docker.example .env.docker
nano .env.docker
```

Yang harus diisi:
- `APP_URL=https://avsec.example.com`
- `DB_PASSWORD=` (sama dgn yang di-CREATE USER MySQL)
- `MAIL_*` (kalau pakai SMTP)
- `APP_KEY=` (kosongkan dulu, akan diisi di langkah 4.2)

```bash
chmod 600 .env.docker
```

### 3.5. Setup permission storage (sekali)

```bash
sudo bash deploy/setup-host.sh
```

Script ini akan:
- Buat struktur `storage/` dan `bootstrap/cache/`
- Set owner `33:33` (UID www-data di container)
- Set permission `2775` (setgid) untuk dir, `664` untuk file
- Set permission `750/640` untuk `storage/app/private/` (dokumen rahasia)
- `chmod 600` pada `.env.docker`
- `chmod 750` pada project root

---

## 4. Build & Start

### 4.1. Build image + asset

```bash
cd /upt/apps
chmod +x deploy/build.sh
./deploy/build.sh
```

Script ini akan:
1. `npm ci` + `npm run build` (asset Vite di host)
2. `docker compose build app`

### 4.2. Generate APP_KEY (sekali)

```bash
docker compose run --rm app php artisan key:generate --show
# salin output ke .env.docker pada baris APP_KEY=
```

### 4.3. Start semua container

```bash
docker compose up -d
docker compose ps
docker compose logs -f app
```

Tunggu sampai semua status `Up (healthy)`.

### 4.4. Verifikasi internal

```bash
# PHP-FPM responsive
curl -I http://127.0.0.1:9000/  # connection refused = OK (FastCGI bukan HTTP)

# DB reachable dari container
docker compose exec app php artisan db:show

# Migration jalan
docker compose exec app php artisan migrate:status
```

---

## 5. Setup Nginx Host

```bash
# Salin template
sudo cp /upt/apps/deploy/nginx-host.conf.example /etc/nginx/sites-available/avsec

# Edit: ganti server_name
sudo nano /etc/nginx/sites-available/avsec
#   - server_name avsec.example.com;
#   - root /upt/apps/public;  (sudah benar)
#   - ssl_certificate path (akan diisi certbot)

# Aktifkan site
sudo ln -sf /etc/nginx/sites-available/avsec /etc/nginx/sites-enabled/avsec
sudo rm -f /etc/nginx/sites-enabled/default

# Test config (akan error karena cert belum ada — biarkan dulu)
sudo nginx -t || true

# Sementara comment listen 443 block, aktifkan listen 80 saja, lalu reload
# kemudian jalankan certbot:
sudo certbot --nginx -d avsec.example.com

# Certbot akan auto-edit nginx config + cert + reload
sudo nginx -t
sudo systemctl reload nginx
```

### 5.1. Verifikasi end-to-end

```bash
curl -I https://avsec.example.com/up
# Harus: HTTP/2 200
```

Buka browser: https://avsec.example.com → halaman login muncul,
asset CSS/JS load, tidak ada 404.

---

## 6. Backup Otomatis

### 6.1. Direktori backup

```bash
sudo mkdir -p /var/backup/avsec
sudo chmod 700 /var/backup/avsec
```

### 6.2. Cron job backup (sudo crontab -e)

```cron
# Backup database native MySQL (jam 02:00)
0 2 * * * mysqldump -uavsec -pPASSWORD --single-transaction --routines --events avsec | gzip > /var/backup/avsec/db-$(date +\%F).sql.gz

# Backup storage (uploads + dokumen PMIK) - jam 03:00
0 3 * * * tar -czf /var/backup/avsec/storage-$(date +\%F).tgz -C /upt/apps storage --exclude=storage/framework --exclude=storage/logs

# Hapus backup > 30 hari
0 4 * * * find /var/backup/avsec -name "*.gz" -mtime +30 -delete
0 4 * * * find /var/backup/avsec -name "*.tgz" -mtime +30 -delete
```

> Tip: simpan password MySQL di `~/.my.cnf` dengan `chmod 600` agar
> tidak terlihat di proses listing:
> ```ini
> [client]
> user=avsec
> password=PASSWORD
> ```
> Lalu cron pakai `mysqldump --defaults-file=/root/.my.cnf avsec | gzip > ...`.

### 6.3. Off-site backup (rekomendasi)

Sinkronkan `/var/backup/avsec` ke object storage (S3, B2, dll) atau
server lain via `rclone` / `rsync`.

---

## 7. Deploy Update (rilis baru)

```bash
cd /upt/apps

# 1. Pull kode terbaru
git pull origin main

# 2. Rebuild image + asset
./deploy/build.sh

# 3. Recreate container
docker compose up -d app queue scheduler

# 4. Cek logs
docker compose logs -f app
```

Migration otomatis dijalankan oleh entrypoint container `app`.

### Rollback cepat

```bash
git checkout <commit-sebelumnya>
./deploy/build.sh
docker compose up -d
```

Database rollback (jika perlu):
```bash
gunzip < /var/backup/avsec/db-YYYY-MM-DD.sql.gz | mysql -uavsec -p avsec
```

---

## 8. Operasi Sehari-hari

### Status & log

```bash
docker compose ps
docker compose logs --tail=200 app
docker compose logs --tail=200 queue
docker compose logs --tail=200 scheduler

# Tail log Laravel langsung dari host (storage di-bind):
tail -f /upt/apps/storage/logs/laravel.log
tail -f /upt/apps/storage/logs/scheduler.log
```

### Masuk container

```bash
docker compose exec app bash
# di dalam:
php artisan tinker
php artisan queue:failed
php artisan migrate:status
```

### Restart queue (setelah deploy code)

```bash
docker compose exec app php artisan queue:restart
docker compose restart queue
```

### Clear & rebuild cache

```bash
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize
```

### Backup manual

```bash
# Database
mysqldump -uavsec -p --single-transaction avsec | gzip > /tmp/backup-$(date +%F-%H%M).sql.gz

# Storage (uploads)
tar -czf /tmp/storage-$(date +%F-%H%M).tgz -C /upt/apps storage --exclude=storage/framework --exclude=storage/logs
```

### Restore

```bash
# Database
gunzip < /var/backup/avsec/db-2026-01-15.sql.gz | mysql -uavsec -p avsec

# Storage (HATI-HATI: akan menimpa file yang ada)
docker compose stop app queue scheduler
sudo tar -xzf /var/backup/avsec/storage-2026-01-15.tgz -C /upt/apps
sudo bash deploy/setup-host.sh   # re-fix permission
docker compose up -d
```

---

## 9. Troubleshooting

| Gejala | Diagnosa & Solusi |
|---|---|
| `502 Bad Gateway` | Container `app` belum healthy. Cek `docker compose logs app`. Pastikan port 9000 tidak dipakai proses lain di host. |
| `Permission denied` di storage | Jalankan `sudo bash deploy/setup-host.sh` lagi. UID di host harus 33. |
| Browsershot error `chromium not found` | Cek `PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium` di `.env.docker`. |
| Browsershot error `Cannot find module 'puppeteer'` | Image lama? Rebuild: `./deploy/build.sh`. |
| Asset 404 (`/build/...`) | `npm run build` belum jalan. `cd /upt/apps && npm run build`. Cek isi `public/build/`. |
| `/storage/...` 404 | Symlink `public/storage` tidak terbuat. Jalankan `docker compose exec app php artisan storage:link --relative --force`. |
| Migration gagal saat startup | Cek `docker compose logs app`. Set `RUN_MIGRATIONS=false` di `.env.docker`, lalu jalankan manual: `docker compose exec app php artisan migrate --force`. |
| Queue tidak jalan | `docker compose logs queue` + `docker compose exec app php artisan queue:failed`. |
| MySQL `Connection refused` | Cek `bind-address = 172.17.0.1` di mysqld.cnf. Cek firewall: `sudo ufw status`. Test: `docker compose run --rm app mysqladmin ping -h host.docker.internal -uavsec -p`. |
| MySQL `Access denied for user 'avsec'@'172.17.0.x'` | Pattern user salah. `CREATE USER 'avsec'@'172.%.%.%'` lalu `GRANT ALL ... TO 'avsec'@'172.%.%.%'`. |
| Login HTTPS gagal redirect loop | `SESSION_SECURE_COOKIE=true` tapi nginx tidak set `X-Forwarded-Proto`. Cek nginx config: `fastcgi_param HTTPS on;`. |
| Scheduler log kosong | Cek `docker compose logs scheduler`. Pastikan crontab di-bind read-only dan format benar (LF, ada newline di akhir). |
| `host.docker.internal` not resolved | Docker < 20.10. Update Docker, atau ganti `DB_HOST=172.17.0.1` langsung. |

---

## 10. Checklist Verifikasi Pasca-Deploy

- [ ] `curl -I https://avsec.example.com/up` -> `200 OK`
- [ ] `docker compose ps` -> 3 container `Up (healthy)`
- [ ] Login user berhasil; sesi tersimpan di tabel `sessions` MySQL
- [ ] Upload dokumen PMIK -> file tersimpan di `/upt/apps/storage/app/private/private/documents/`
- [ ] Generate PDF (Browsershot) -> tidak error chromium / puppeteer
- [ ] Akses `https://avsec.example.com/storage/sample.jpg` (file public) -> serve normal
- [ ] Akses `https://avsec.example.com/storage/../app/private/...` -> 404 (tidak bocor)
- [ ] `docker compose down && docker compose up -d` -> data MySQL & storage utuh
- [ ] Backup harian (`/var/backup/avsec`) ter-generate setelah jadwal cron
- [ ] `tail /upt/apps/storage/logs/scheduler.log` -> log update tiap menit

---

## 11. Catatan Keamanan

### Network isolation
- **PHP-FPM** hanya listen di `127.0.0.1:9000` host, tidak ter-ekspos publik.
- **MySQL** hanya bind di `172.17.0.1` (docker bridge), tidak terekspos publik.
- **HTTPS** wajib aktif di nginx host (Let's Encrypt + HSTS).

### Filesystem
- **Project root** `chmod 750`, owner deploy user.
- **`.env.docker`** `chmod 600`, owner root. Tidak di-commit.
- **`storage/app/private/`** `chmod 750` dir, `640` file. Owner `www-data`(33).
- **Dokumen rahasia PMIK** (`storage/app/private/private/documents/dokumen-rahasia/`)
  hanya dapat diakses via PHP controller dengan auth check, **tidak** ter-serve
  langsung oleh nginx (tidak ada path `location /storage/private` di config).

### Aplikasi
- `APP_DEBUG=false`, `APP_ENV=production`.
- `OPcache validate_timestamps=0` -> wajib rebuild image untuk apply code change.
- `SESSION_SECURE_COOKIE=true` di production HTTPS.
- Session driver `database` -> safe untuk multi-replica jika nanti scale.

### Backup
- Backup database harian + storage harian.
- Retensi 30 hari di server.
- Off-site copy ke S3/B2 (rekomendasi).

---

## 12. Skala Lanjutan (kalau nanti perlu)

| Kebutuhan | Solusi |
|---|---|
| Performa session/cache lebih baik | Tambah service `redis` di compose, ganti `SESSION_DRIVER=redis`, `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`. |
| Multi-replica app | Hapus `RUN_MIGRATIONS` dari entrypoint app; jalankan migrate via deploy job terpisah. Tambah upstream block di nginx. |
| Storage besar | Ganti `FILESYSTEM_DISK=s3` untuk `storage/app/public`. Private docs tetap lokal untuk audit. |
| Monitor queue | Install Laravel Horizon (butuh Redis). |
| Observability | Tambah Prometheus exporter + Grafana dashboard. |

---

## 13. Struktur File Deployment

```
/upt/apps/
├── .dockerignore
├── .env.docker                  # NEVER commit, chmod 600
├── .env.docker.example          # template, safe to commit
├── docker-compose.yml
├── docker/
│   ├── php/
│   │   ├── Dockerfile           # multi-stage
│   │   ├── php.ini              # production tuning
│   │   ├── opcache.ini
│   │   ├── www.conf             # php-fpm pool
│   │   └── entrypoint.sh        # migrate + cache + privilege drop
│   └── scheduler/
│       └── crontab              # cron schedule
├── deploy/
│   ├── build.sh                 # npm build + docker build
│   ├── setup-host.sh            # init permissions (run sekali)
│   ├── nginx-host.conf.example
│   └── README.md                # this file
├── storage/                     # bind mount (uploads, logs)
└── public/                      # bind mount (static, build)
```
