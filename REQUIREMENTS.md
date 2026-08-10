# Lentera Project — Trading Consultant Website

Dokumen kebutuhan (requirements) & ruang lingkup pekerjaan.

---

## 1. Ringkasan Produk

**Lentera Project** adalah website profil & portofolio untuk **jasa konsultan trading**.
Website menampilkan profil tim (konsultan/trader), rekam jejak performa (PnL/Profit & Loss),
serta layanan konsultasi. Data PnL dapat **diinput manual** oleh admin, atau **disinkronkan
otomatis dari Binance Futures API**.

- **Tagline:** Menerangi jalan trading Anda.
- **Target user:** calon klien yang ingin melihat kredibilitas tim & track record sebelum
  menggunakan jasa konsultasi/mentoring/signal.

---

## 2. Tech Stack

| Layer      | Teknologi                                                        |
|------------|-----------------------------------------------------------------|
| Backend    | Laravel 13 (PHP 8.4)                                             |
| Frontend   | Vue 3 + Inertia.js + TypeScript                                  |
| Styling    | Tailwind CSS v4 + komponen shadcn-vue                           |
| Database   | MySQL 8 (lokal, db: `lentera_project`)                          |
| Auth       | Laravel Fortify (login admin, 2FA, passkey — bawaan starter kit) |
| Build      | Vite                                                             |
| Integrasi  | Binance USD-M Futures REST API (signed request HMAC-SHA256)      |
| Chart      | Komponen SVG kustom (equity curve + bar bulanan), tanpa dependency |

---

## 3. Aktor / Role

1. **Pengunjung (publik)** — melihat landing, tim, profil trader, performa PnL, kirim pesan.
2. **Admin** — login ke dashboard, CRUD member/trade, sinkron Binance, kelola leads,
   testimoni, dan layanan.

---

## 4. Halaman Publik

| Halaman            | Route         | Isi |
|--------------------|---------------|-----|
| Landing / Home     | `/`           | Hero, statistik agregat (total PnL, win rate, jumlah trade), layanan, tim ringkas, testimoni, CTA kontak |
| Tim                | `/team`       | Daftar member (kartu) + link ke profil |
| Profil Trader      | `/team/{slug}`| Bio, spesialisasi, sosial media, statistik, equity curve, tabel riwayat PnL, chart PnL bulanan |
| Performa / Track Record | `/performance` | Rekap performa seluruh tim: total PnL, win rate, chart, tabel trade terbaru, filter per member |
| Kontak             | `/contact`    | Form kontak (nama, email, pesan) → tersimpan sebagai lead |

## 5. Halaman Admin (butuh login)

| Halaman              | Route                  | Fungsi |
|----------------------|------------------------|--------|
| Dashboard            | `/dashboard`           | Ringkasan KPI, trade terbaru, lead terbaru |
| Kelola Member        | `/admin/members`       | List, create, edit, hapus member |
| Kelola Trade (PnL)   | `/admin/trades`        | List + filter, input manual trade/PnL, edit, hapus |
| Sinkron Binance      | `/admin/binance`       | Set/uji API key, tarik riwayat income (realized PnL) → jadi trade |
| Leads / Pesan        | `/admin/leads`         | List pesan masuk, tandai dibaca/selesai |
| Testimoni            | `/admin/testimonials`  | CRUD testimoni |
| Layanan              | `/admin/services`      | CRUD layanan konsultasi |

---

## 6. Model Data

### members
- `id, name, slug, title/role, tagline, bio, avatar, specialization`
- `experience_years, joined_at, location`
- sosial: `telegram, instagram, twitter, tradingview, website`
- `is_active, is_featured, sort_order`
- Statistik dihitung dari relasi `trades` (total_pnl, win_rate, total_trades, best_trade, dsb).

### trades  (satu baris = satu transaksi / catatan PnL)
- `id, member_id`
- `symbol` (mis. BTCUSDT), `market` (futures/spot), `side` (long/short)
- `leverage, entry_price, exit_price, quantity`
- `pnl` (nominal USDT), `roi` (persen), `fee`
- `opened_at, closed_at, status` (open/closed)
- `source` (manual/binance), `binance_id` (unik untuk anti-duplikat), `notes`
- Realized PnL bulanan diagregasi on-the-fly dari kolom `pnl` + `closed_at`.

### services (layanan konsultasi)
- `id, title, description, icon, price_label, features(json), sort_order, is_active`

### testimonials
- `id, author_name, author_role, quote, rating, avatar, is_active, sort_order`

### leads (pesan kontak)
- `id, name, email, phone, subject, message, status(new/read/done), created_at`

### users (admin) — bawaan starter kit, dipakai untuk login admin.

---

## 7. Integrasi Binance (opsional, aktif bila API key diisi)

- `app/Services/BinanceService.php` — signed request (HMAC-SHA256) ke Binance USD-M Futures.
- Endpoint utama: `/fapi/v1/income?incomeType=REALIZED_PNL` untuk menarik realized PnL,
  dipetakan menjadi baris `trades` (source=binance, anti-duplikat via `binance_id`).
- Endpoint `/fapi/v2/account` untuk uji koneksi & saldo.
- Bila `BINANCE_API_KEY`/`BINANCE_API_SECRET` kosong → fitur sinkron nonaktif, input manual tetap jalan.
- Kredensial disimpan di `.env` (bisa juga per-member di kolom terenkripsi — versi awal: global `.env`).

---

## 8. Data Awal (Seeder)

- **Admin:** email `admin@lentera.id` / password `password` (silakan ganti).
- **2 Member** (keduanya pria, avatar sketsa SVG):
  1. Trader A — spesialis Crypto Futures.
  2. Trader B — spesialis Price Action / Swing.
- Sampel trade realistis per member (± beberapa bulan) untuk mengisi chart & statistik.
- Layanan, testimoni, dan beberapa lead contoh.

---

## 9. Non-Fungsional
- Responsif (mobile-first), dark/light mode (bawaan starter kit).
- Angka finansial diformat (USDT, persen, warna hijau/merah untuk profit/loss).
- Validasi form di server (Form Request).
- Aman: signed Binance request, kredensial di `.env`, route admin dilindungi middleware `auth`.

---

## 9b. Fitur Tambahan (update sesuai permintaan)

- **Warna brand:** hijau (emerald/green). Profit hijau, loss merah, rating bintang emas.
- **Navbar:** menu dipepet ke logo (rata kiri) + dropdown **Jadwal Konsultasi** (jam & jenis konsultasi).
- **Kalender PnL Harian** di `/performance`: tiap tanggal menampilkan PnL harian (hijau/merah);
  klik tanggal → **modal** berisi daftar transaksi hari itu, dengan **search** (simbol/konsultan/arah)
  dan **pagination**. Navigasi antar bulan.
- **Sensor PnL (privasi):** angka nominal `$` **diburamkan** untuk pengunjung yang belum login
  (win rate & ROI % tetap tampil). Setelah **login/daftar akun**, angka tampil penuh.
  - Guest → tersensor. User login (siapa pun yang mendaftar) → penuh.
  - Akses: **Login** `/login`, **Daftar** `/register` (link tersedia di navbar & footer).
- **Booking konsultasi bergaya Google Calendar** (`/schedule`): pilih tanggal di kalender →
  pilih slot jam yang tersedia (jam operasional otomatis, slot penuh/lewat dinonaktifkan) →
  isi data → konfirmasi. Tersimpan & bisa dikelola admin di menu **Jadwal Konsultasi**
  (status: menunggu/dikonfirmasi/selesai/dibatalkan). Diakses via tombol "Jadwalkan Sekarang"
  / dropdown / CTA "Konsultasi".
- **Halaman error branded** (404/403/500/503/419) bertema Lentera (hijau, mandiri tanpa JS).
- **SEO & Analytics:**
  - Meta description, keywords, Open Graph, Twitter Card, canonical, JSON-LD Organization.
  - `robots.txt` & `sitemap.xml` **dinamis** (otomatis mengikuti domain + daftar profil member).
  - **Google Analytics (GA4)** & **Google Search Console** aktif otomatis bila diisi di `.env`
    (`GOOGLE_ANALYTICS_ID`, `GOOGLE_SITE_VERIFICATION`), lalu `php artisan config:clear`.

## 9c. Akun & Akses

| Peran | URL | Kredensial |
|-------|-----|------------|
| Login admin | `/login` | `admin@lentera.id` / `password` |
| Daftar publik (lihat PnL penuh) | `/register` | buat sendiri |
| Dashboard admin | `/dashboard` | setelah login |

## 10. Cara Menjalankan
```bash
# 1. Dependency
composer install
npm install

# 2. Konfigurasi .env sudah diset (DB: lentera_project, MySQL root)
php artisan key:generate      # bila perlu

# 3. Migrasi + seed data awal
php artisan migrate:fresh --seed

# 4. Jalankan (dev)
composer run dev              # menjalankan server + vite + queue
#   atau terpisah:
php artisan serve
npm run dev

# Login admin: http://localhost:8000/login  (admin@lentera.id / password)
```
