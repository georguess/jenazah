# SI Manajemen Jenazah dan Identifikasi Terintegrasi

Baseproject tugas akhir — Laravel 11 (monolit Blade) + PostgreSQL.

## Tema
Sistem pencatatan jenazah (temuan/korban) dan laporan orang hilang, dengan
alur pencocokan data antemortem (ciri sebelum meninggal, dari laporan
keluarga) terhadap data postmortem (ciri hasil pemeriksaan forensik), sampai
verifikasi identifikasi.

## Peran pengguna
| Role | Deskripsi |
|---|---|
| `admin` | Kelola user, verifikasi akhir kecocokan identifikasi |
| `petugas_forensik` | Input data postmortem, ajukan usulan kecocokan |
| `petugas_administrasi` | Input data jenazah masuk & laporan kehilangan |
| `keluarga_pelapor` | Mengajukan laporan kehilangan, melengkapi data antemortem |

## Alur interaksi singkat
1. Petugas administrasi mencatat jenazah yang ditemukan → `jenazah`.
2. Keluarga/petugas mencatat laporan kehilangan → `laporan_kehilangan`.
3. Keluarga melengkapi ciri-ciri sebelum meninggal → `data_antemortem`.
4. Petugas forensik memeriksa jenazah → `data_postmortem`.
5. Sistem/petugas mengajukan pasangan calon cocok → `kecocokan_identifikasi`.
6. Admin/verifikator menyetujui atau menolak kecocokan.

Rancangan skema lengkap (atribut, tipe data, alasan): lihat `docs/rancangan-basis-data.md`.

## Menjalankan dari nol

Prasyarat: PHP >= 8.2, Composer, PostgreSQL >= 13, ekstensi PHP `pgsql` & `pdo_pgsql` aktif.

```bash
# 1. Clone
git clone <url-repo-ini> si-manajemen-jenazah
cd si-manajemen-jenazah

# 2. Install dependency PHP
composer install

# 3. Siapkan file environment
cp .env.example .env
php artisan key:generate

# 4. Buat database PostgreSQL kosong
createdb si_manajemen_jenazah
# atau lewat psql:
# psql -U postgres -c "CREATE DATABASE si_manajemen_jenazah;"

# 5. Sesuaikan kredensial DB di .env jika perlu
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=si_manajemen_jenazah
# DB_USERNAME=postgres
# DB_PASSWORD=

# 6. Jalankan migrasi + seeder
php artisan migrate --seed

# 7. Jalankan server
php artisan serve
# buka http://127.0.0.1:8000
```

Akun contoh hasil seeder (password semua: `password`):

| Email | Role |
|---|---|
| admin@simjenazah.test | admin |
| forensik1@simjenazah.test | petugas_forensik |
| admin1@simjenazah.test | petugas_administrasi |
| pelapor1@simjenazah.test | keluarga_pelapor |

## Reset data
```bash
php artisan migrate:fresh --seed
```

## Struktur dokumen tugas akhir
- `docs/rancangan-basis-data.md` — entitas, atribut, kunci, relasi, alasan tipe data & batasan integritas.
- `docs/arsitektur.md` — pilihan arsitektur beserta alasan dan trade-off.
- `docs/pembagian-peran.md` — template pembagian peran tim (isi dengan nama anggota).
- `docs/pengguna-uji-kebergunaan.md` — template daftar calon pengguna uji kebergunaan (isi dengan nama nyata yang sudah dihubungi).

## Catatan
File-file di atas ditulis manual di lingkungan sandbox tanpa PHP/Composer aktif,
sehingga belum sempat dieksekusi/divalidasi langsung dengan `php artisan migrate`.
Jalankan langkah di atas di mesin lokal dan periksa jika ada error sintaks kecil
sebelum dipakai sebagai baseproject final.
