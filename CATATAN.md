# CATATAN — Pembagian Kerja Kelompok

> Diisi oleh anggota kelompok. Nama sengaja dikosongkan. Kontribusi tiap anggota
> harus terlihat pada riwayat commit repositori (rubrik P2), jadi setiap orang
> meng-commit bagiannya sendiri.

## Anggota

| No | Nama | NIM | Peran utama |
|---|---|---|---|
| 1 | | | Basis data & model |
| 2 | | | Antarmuka React & komponen |
| 3 | | | Autentikasi, middleware, Gate & Policy |
| 4 | | | Pengujian, uji kebergunaan & dokumentasi |

## Siapa mengerjakan apa

| Bagian | Berkas utama | Dikerjakan oleh | Ditelaah oleh |
|---|---|---|---|
| Migrasi baru (CHECK role, `diajukan_oleh`, perbaikan `status_identifikasi`) | `database/migrations/2026_10_19_*` | | |
| Model, relasi, factory, seeder | `app/Models`, `database/factories`, `database/seeders` | | |
| Modul Jenazah (7 aksi) | `JenazahController`, `pages/jenazah/*` | | |
| Modul Laporan Kehilangan | `LaporanKehilanganController`, `pages/laporan/*` | | |
| Modul Antemortem | `DataAntemortemController`, `pages/antemortem/*` | | |
| Modul Postmortem | `DataPostmortemController`, `pages/postmortem/*` | | |
| Modul Kecocokan + verifikasi | `KecocokanIdentifikasiController`, `pages/kecocokan/*` | | |
| Modul Dokumen pendukung | `DokumenPendukungController`, `pages/dokumen/*` | | |
| Kelola pengguna | `UserController`, `pages/pengguna/*` | | |
| Komponen bersama (Kartu, Kosong, PesanFlash, TombolHapus, ...) | `resources/js/components` | | |
| Middleware peran, Gate, Policy, menu | `PastikanPeran`, `app/Policies`, `HandleInertiaRequests` | | |
| Feature test | `tests/Feature` | | |
| Dokumen | `README.md`, `docs/*` | | |

## Bukti yang wajib disiapkan sendiri

- [ ] Tangkapan layar tiap tugas studio (Blade vs React tidak relevan lagi — gunakan versi React).
- [ ] Rekaman layar satu putaran penuh modul utama (daftar → tambah → galat validasi → simpan → ubah → hapus).
- [ ] Rekaman layar uji "ketik URL langsung": masuk sebagai petugas/keluarga, ketik alamat khusus admin, hasilnya 403.
- [ ] Nama calon pengguna uji kebergunaan di `docs/pengguna-uji-kebergunaan.md`.

## Log keputusan

| Tanggal | Keputusan | Alasan | Disetujui |
|---|---|---|---|
| | Pindah dari monolit Blade ke monolit Inertia + React | Lihat `docs/arsitektur.md` | |
| | Matriks RBAC final | Lihat `docs/rbac.md` bagian 8 | |
