# Akses & Uji Login per Role — Lentera Project

Dokumen ini menjelaskan **role yang ada**, **akun uji**, dan **cara memverifikasi hak akses tiap role**.

- Base URL (dev): `http://127.0.0.1:8000`
- Semua akun seeder memakai password: **`password`**
- Menyiapkan akun uji: `php artisan db:seed` (atau `php artisan migrate:fresh --seed`)

---

## 1. Role yang tersedia

Kolom `users.role` menampung 3 nilai. Dicek lewat `User::isAdmin()` / `User::isTeam()`,
dijaga middleware `admin` (`EnsureAdmin`) & `team` (`EnsureTeam`), dan redirect diatur di `LoginResponse`.

| Role | Untuk siapa | Area utama | Login mendarat di |
|------|-------------|------------|-------------------|
| `admin` | Pengelola / backoffice | `/dashboard` + semua `/admin/*` | `/dashboard` |
| `team` | Konsultan/trader | `/panel` (atur ketersediaan sendiri) | `/panel` |
| `user` (atau tanpa role) | Klien/member | Situs publik + `/schedule` | `/` (beranda) |

> Registrasi mandiri (`/register`) membuat akun **tanpa role** → diperlakukan sebagai **klien (`user`)**.

---

## 2. Akun uji (dari seeder)

| Role | Email | Password | Catatan |
|------|-------|----------|---------|
| admin | `admin@lentera.id` | `password` | Akses penuh backoffice |
| team | `azharuddin@lentera.id` | `password` | Terhubung ke profil konsultan Azharuddin |
| team | `andra@lentera.id` | `password` | Terhubung ke profil konsultan Andra |
| user/klien | — | — | Buat sendiri (lihat §5) |

Login manual lewat halaman `/login` (email + password).

---

## 3. Matriks akses per menu

Legenda: ✅ boleh · ⛔ ditolak/diarahkan · ↪️ dialihkan ke area role-nya

| Menu / Route | admin | team | user/klien |
|---|:--:|:--:|:--:|
| `/dashboard` (ringkasan backoffice) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/members` (Team) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/trades` (Trade & PnL) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/signals` (Signal) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/bookings` (Jadwal Konsultasi) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/leads` (Pesan Masuk) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/services`, `/admin/testimonials` | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/admin/binance` (Sinkron Binance) | ✅ | ↪️ `/panel` | ↪️ `/` |
| `/panel` (Panel Konsultan) | ↪️ `/dashboard` | ✅ | ↪️ `/` |
| `/schedule` (Jadwalkan Konsultasi) | ✅ | ✅ | ✅ (publik) |
| `/signals` (Signal publik) | ✅ + detail premium terbuka | ✅ + detail premium terbuka | ✅, detail premium **terkunci** |
| Halaman publik lain (`/`, `/team`, `/performance`, `/contact`) | ✅ | ✅ | ✅ |

Sidebar juga otomatis menyesuaikan: admin melihat menu backoffice lengkap, team hanya "Panel Saya" & "Jadwalkan Konsultasi".

---

## 4. Langkah verifikasi cepat

### A. Admin
1. Login `admin@lentera.id` / `password` → harus mendarat di **`/dashboard`**.
2. Sidebar menampilkan grup: Ringkasan, Performa & Tim, **Sinyal & Market**, Konsultasi, Konten Web.
3. Buka `/admin/signals` → bisa buat/edit/hapus signal.
4. Buka `/panel` → **dialihkan ke `/dashboard`** (bukan area admin).

### B. Team (konsultan)
1. Login `azharuddin@lentera.id` / `password` → harus mendarat di **`/panel`**.
2. Sidebar hanya "Panel Saya" & "Jadwalkan Konsultasi".
3. Buka `/admin/members` (atau `/admin/*` mana pun) → **dialihkan ke `/panel`**.
4. Di `/panel` bisa mengosongkan jadwal/cuti untuk dirinya sendiri.

### C. User / klien
1. Buat akun (lihat §5) lalu login → harus mendarat di **`/` (beranda)**.
2. Buka `/dashboard` atau `/admin/...` atau `/panel` → **dialihkan ke `/`**.
3. Buka `/signals` → sinyal premium tampil sebagai teaser **terkunci** (tanpa entry/SL/TP), ada tombol "Langganan untuk Buka".
4. Buka `/schedule` → bisa membuat booking; setelah submit diarahkan ke **halaman status booking** miliknya (link privat via token) untuk upload bukti bayar & memberi ulasan.

---

## 5. Membuat akun uji role `user`/klien

**Cara 1 — Registrasi:** buka `/register`, daftar akun baru (otomatis tanpa role = klien).

**Cara 2 — Tinker:**
```bash
php artisan tinker --execute="\App\Models\User::create(['name'=>'Klien Uji','email'=>'klien@lentera.id','password'=>bcrypt('password'),'role'=>'user']);"
```
Lalu login `klien@lentera.id` / `password`.

---

## 6. Uji otomatis (Pest)

Hak akses per role sudah dicakup test:

```bash
php artisan test --filter="membatasi akses admin|team bisa buka panel|signal"
```

Test kunci di `tests/Feature/AppSmokeTest.php`:
- `membatasi akses admin untuk role team & user` — team ↪️ `/panel`, user ↪️ `/`, dan `/dashboard`/`/panel` untuk user ↪️ `/`.
- `team bisa buka panel & kosongkan jadwal sendiri`.
- `halaman publik signal mengunci detail premium untuk tamu` — `canSeePremium=false` untuk tamu, premium tanpa detail.
