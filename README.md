# SI Manajemen Jenazah dan Identifikasi Terintegrasi

Proyek kelompok mata kuliah Web Framework (INF625313).
**Laravel 13 + Inertia 3 + React (JavaScript/JSX) + PostgreSQL**, dibangun di atas
starter kit React resmi Laravel (autentikasi Fortify, tata letak bilah samping).

## Tema

Pencatatan jenazah temuan/korban dan laporan orang hilang, lalu pencocokan data
**antemortem** (ciri sebelum meninggal, dari keluarga) dengan data
**postmortem** (hasil pemeriksaan forensik) sampai **verifikasi** identitas.

## Peran pengguna

| Peran | Tugas utama |
|---|---|
| `admin` | Kelola pengguna, verifikasi akhir kecocokan, statistik |
| `petugas_administrasi` | Catat jenazah, laporan kehilangan, dokumen pendukung |
| `petugas_forensik` | Isi data postmortem, ajukan kecocokan |
| `keluarga_pelapor` | Buat laporan kehilangan dan lengkapi data antemortem miliknya |

Matriks lengkap peran × modul × aksi: [`docs/rbac.md`](docs/rbac.md).

## Alur singkat

1. Petugas administrasi mencatat jenazah → `jenazah` (status `belum_teridentifikasi`).
2. Keluarga/petugas membuat laporan kehilangan → `laporan_kehilangan`.
3. Keluarga melengkapi ciri sebelum meninggal → `data_antemortem`.
4. Petugas forensik memeriksa jenazah → `data_postmortem`.
5. Petugas forensik mengajukan pasangan cocok → `kecocokan_identifikasi` (jenazah menjadi `proses`).
6. Admin menyetujui/menolak; bila disetujui jenazah `teridentifikasi` dan laporan `cocok`.

## Prasyarat

| Perangkat | Versi |
|---|---|
| PHP | 8.3 atau lebih baru, ekstensi `pdo_pgsql`, `pgsql`, `mbstring`, `intl`, `zip` |
| Composer | 2.x |
| Node.js + npm | Node 22 LTS (minimal 20.19) |
| PostgreSQL | 13 atau lebih baru |

## Menjalankan dari nol ("uji laptop kosong")

```bash
# 1. Salin repositori
git clone <url-repo-kelompok> si-manajemen-jenazah
cd si-manajemen-jenazah

# 2. Dependensi PHP dan JavaScript
composer install
npm install

# 3. Berkas lingkungan
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate

# 4. Buat dua basis data PostgreSQL kosong (aplikasi + pengujian)
createdb -U postgres si_manajemen_jenazah
createdb -U postgres si_manajemen_jenazah_test
#   atau lewat psql:
#   psql -U postgres -c "CREATE DATABASE si_manajemen_jenazah;"
#   psql -U postgres -c "CREATE DATABASE si_manajemen_jenazah_test;"

# 5. Sesuaikan DB_USERNAME / DB_PASSWORD di .env bila perlu, lalu:
php artisan migrate --seed

# 6. Jalankan (dua terminal) ...
php artisan serve
npm run dev
#    ... atau satu perintah untuk keduanya:
composer dev

# Buka http://127.0.0.1:8000
```

Bila halaman tampil kosong tanpa gaya, berarti `npm run dev` belum berjalan
(atau jalankan `npm run build` sekali untuk berkas produksi).

## Akun contoh (kata sandi semua: `password`)

| Email | Peran |
|---|---|
| admin@simjenazah.test | admin |
| forensik1@simjenazah.test | petugas_forensik |
| forensik2@simjenazah.test | petugas_forensik |
| admin1@simjenazah.test | petugas_administrasi |
| pelapor1@simjenazah.test | keluarga_pelapor |

Seeder juga membuat data contoh lewat factory (±30 jenazah, ±24 laporan,
termasuk nama sepanjang ±60 huruf) agar paginasi dan tata letak teruji.

Akun dari halaman **Daftar** selalu berperan `keluarga_pelapor` dan harus
memverifikasi email; di lokal, tautan verifikasi tertulis di
`storage/logs/laravel.log` (`MAIL_MAILER=log`). Akun petugas dibuat admin lewat
menu **Pengguna**.

## Pengujian

Pengujian **wajib PostgreSQL** (migrasi `dokumen_pendukung` memakai
`CHECK num_nonnulls(...)` yang tidak ada di SQLite). `phpunit.xml` memakai basis
data `si_manajemen_jenazah_test` dengan kredensial dari `.env`.

```bash
php artisan test
```

Cakupan utama:

| Berkas | Isi |
|---|---|
| `tests/Feature/OtorisasiPeranTest.php` | Tiap peran × rute utama → 200 atau 403; tamu → `/login`; aksi tulis lewat "curl" → 403; halaman 403 ramah |
| `tests/Feature/KepemilikanDataTest.php` | Keluarga hanya melihat/mengubah data miliknya (disaring di kueri) |
| `tests/Feature/ValidasiFormulirTest.php` | Galat per isian + isian lama kembali; aturan `exists:`, `in:`, `unique:` |
| `tests/Feature/PostRedirectGetTest.php` | Tujuh aksi modul jenazah, redirect + flash, unggah/unduh dokumen, registrasi |
| `tests/Feature/VerifikasiKecocokanTest.php` | Alur ajukan → verifikasi admin, penguncian setelah verifikasi |
| `tests/Feature/MenuDanKewenanganTest.php` | Menu per peran, props `can`, tanpa N+1 |
| `tests/Feature/MigrasiDanSeederTest.php` | `migrate:fresh --seed` berjalan; CHECK constraint bekerja |

## Perintah berguna

```bash
php artisan route:list --except-vendor   # periksa nama rute modul.aksi
php artisan migrate:fresh --seed         # setel ulang data
vendor/bin/pint                          # rapikan kode PHP
npm run format                           # rapikan kode JSX (Prettier)
```

## Dokumen

- [`docs/arsitektur.md`](docs/arsitektur.md) — monolit Inertia + React: alasan dan trade-off.
- [`docs/rbac.md`](docs/rbac.md) — matriks peran × modul × aksi, middleware, Gate, Policy, menu.
- [`docs/rancangan-basis-data.md`](docs/rancangan-basis-data.md) — skema, tipe data, migrasi tambahan.
- [`CATATAN.md`](CATATAN.md) — pembagian kerja kelompok.
- [`docs/pembagian-peran.md`](docs/pembagian-peran.md), [`docs/pengguna-uji-kebergunaan.md`](docs/pengguna-uji-kebergunaan.md) — templat yang diisi kelompok.

## Catatan versi

Dikembangkan dan diuji dengan Laravel 13.37, inertiajs/inertia-laravel 3.5,
laravel/fortify 1.41, @inertiajs/react 3.9, React 19, Vite 8, PHP 8.3,
PostgreSQL 16. `composer.lock` tidak disertakan; `composer install` akan
memilih versi terbaru yang cocok dengan `composer.json`. Setelah berhasil di
laptop salah satu anggota, commit `composer.lock` dan `package-lock.json` agar
semua anggota memakai versi yang sama.
