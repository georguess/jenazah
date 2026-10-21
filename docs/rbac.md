# Kontrol Akses Berbasis Peran (RBAC)

Dokumen ini adalah sumber kebenaran kewenangan. Setiap baris matriks dibuktikan
oleh feature test (`tests/Feature/OtorisasiPeranTest.php`,
`KepemilikanDataTest.php`, `VerifikasiKecocokanTest.php`,
`MenuDanKewenanganTest.php`).

## 1. Tiga lapis penjagaan

| Lapis | Pertanyaan yang dijawab | Tempat | Contoh |
|---|---|---|---|
| Middleware `auth` + `verified` | Siapa Anda? | `routes/web.php` (semua rute aplikasi) | tamu → dialihkan ke `/login` |
| Middleware `peran` (`PastikanPeran`) | Boleh membuka kelompok halaman ini? | `routes/web.php`, alias di `bootstrap/app.php` | `->middleware('peran:admin,petugas_forensik')` |
| Gate / Policy | Boleh melakukan ini pada data **itu**? | `app/Policies/*`, `AppServiceProvider` | `Gate::authorize('update', $laporan)` |
| Saringan kueri | Data mana yang boleh dilihat? | `scopeTerlihatOleh()` di model | keluarga hanya menerima laporannya |

Antarmuka React **tidak** memutuskan apa pun: menu (`menu`) dan tombol (`can`)
dikirim server sebagai hasil Policy yang sama. Menyembunyikan menu hanyalah
kesopanan; mengetik URL langsung tetap menghasilkan **403** (lihat halaman
`resources/js/pages/galat.jsx`).

## 2. Matriks peran × modul × aksi

Keterangan: ✓ boleh · ✗ ditolak (403) · *milik* = hanya data yang `dicatat_oleh` = dirinya.

| Modul | Aksi | admin | petugas_administrasi | petugas_forensik | keluarga_pelapor |
|---|---|:-:|:-:|:-:|:-:|
| **Dasbor** | lihat | ✓ + statistik | ✓ | ✓ | ✓ |
| **Jenazah** | daftar, rincian | ✓ | ✓ | ✓ (baca saja) | ✗ |
| | tambah, ubah | ✓ | ✓ | ✗ | ✗ |
| | hapus | ✓ | ✓ bila **belum** ada postmortem | ✗ | ✗ |
| | ubah status identifikasi | hanya lewat verifikasi kecocokan | ✗ | ✗ | ✗ |
| **Laporan kehilangan** | daftar | ✓ semua | ✓ semua | ✗ | ✓ *milik* (disaring di kueri) |
| | rincian | ✓ | ✓ | ✓ (baca, untuk pencocokan) | ✓ *milik* |
| | tambah | ✓ | ✓ | ✗ | ✓ |
| | ubah | ✓ | ✓ | ✗ | ✓ *milik*, selama status `aktif` |
| | ubah status (aktif/ditutup) | ✓ | ✓ | ✗ | ✗ |
| | hapus | ✓ | ✓ | ✗ | ✗ |
| **Data antemortem** | daftar "Data Antemortem Saya" | — (lewat halaman laporan) | — | ✗ | ✓ *milik* |
| | rincian | ✓ | ✓ | ✓ (baca) | ✓ *milik* |
| | tambah, ubah | ✓ | ✓ | ✗ | ✓ *milik*, laporan `aktif` |
| | hapus | ✓ | ✓ | ✗ | ✗ |
| **Data postmortem** | daftar | — (lewat halaman jenazah) | ✗ | ✓ | ✗ |
| | rincian, tambah | ✓ | ✗ | ✓ | ✗ |
| | ubah, hapus | ✓ | ✗ | ✓ hasil pemeriksaannya sendiri (`diperiksa_oleh`) | ✗ |
| **Kecocokan identifikasi** | daftar, rincian | ✓ | ✗ | ✓ | ✗ |
| | ajukan | ✗ | ✗ | ✓ | ✗ |
| | ubah | ✗ | ✗ | ✓ usulan sendiri (`diajukan_oleh`), status `menunggu` | ✗ |
| | hapus | ✓ status `menunggu` | ✗ | ✓ usulan sendiri, `menunggu` | ✗ |
| | **verifikasi akhir** | ✓ status `menunggu` | ✗ | ✗ | ✗ |
| **Dokumen pendukung** | daftar | — (lewat halaman induk) | ✓ | ✗ | ✗ |
| | rincian, unduh, unggah | ✓ | ✓ | ✗ | ✗ |
| | hapus | ✓ | ✓ unggahan sendiri | ✗ | ✗ |
| **Pengguna** | daftar, tambah, ubah | ✓ | ✗ | ✗ | ✗ |
| | hapus | ✓ akun lain tanpa jejak audit | ✗ | ✗ | ✗ |
| **Akun sendiri** (Pengaturan) | ubah profil/sandi | ✓ | ✓ | ✓ | ✓ |
| | hapus akun | ✗ | ✗ | ✗ | ✓ bila tanpa jejak audit |

"—" berarti tidak ada halaman daftar untuk peran itu (403 bila diketik), tetapi
datanya tetap dapat dibuka dari halaman induk sesuai baris rincian.

## 3. Middleware `peran` dan kelompok rute

`app/Http/Middleware/PastikanPeran.php`, alias `peran` didaftarkan di
`bootstrap/app.php` (tidak ada `Kernel.php` sejak Laravel 11). Semua rute aplikasi
berada di dalam `Route::middleware(['auth', 'verified'])`, lalu dikelompokkan
per himpunan peran:

| Kelompok | Rute |
|---|---|
| semua peran | `laporan.show`, `antemortem.show` |
| `admin,petugas_administrasi,keluarga_pelapor` | `laporan.index/create/store/edit/update`, `antemortem.create/store/edit/update` |
| `keluarga_pelapor` | `antemortem.index` |
| `admin,petugas_administrasi,petugas_forensik` | `jenazah.index/show` |
| `admin,petugas_administrasi` | `jenazah.create/store/edit/update/destroy`, `laporan.destroy`, `antemortem.destroy`, `dokumen.*` |
| `admin,petugas_forensik` | `postmortem.*`, `kecocokan.*` |
| `admin` | `kecocokan.verifikasi`, `pengguna.*` |

Satu-satunya rute di luar kelompok ini adalah rute bawaan Fortify (login,
register, lupa sandi) yang memakai middleware `guest`, dan `/up` (health check).

`verified` dipakai karena `UserSeeder` mengisi `email_verified_at` dan akun buatan
admin langsung ditandai terverifikasi. Akun hasil registrasi publik harus
memverifikasi email (di lingkungan lokal, tautannya tertulis di
`storage/logs/laravel.log` karena `MAIL_MAILER=log`).

## 4. Gate (tanpa objek)

Didefinisikan di `app/Providers/AppServiceProvider.php`:

| Gate | Siapa | Dipakai di |
|---|---|---|
| `kelola-pengguna` | admin | `UserPolicy`, menu "Pengguna" |
| `melihat-statistik` | admin | `DashboardController` (statistik hanya dihitung & dikirim bila diizinkan) |

## 5. Policy (berobjek) — satu Policy per model

| Policy | Catatan kepemilikan |
|---|---|
| `JenazahPolicy` | hapus oleh administrasi hanya bila belum ada postmortem |
| `LaporanKehilanganPolicy` | `dicatat_oleh` untuk keluarga; method tambahan `ubahStatus` |
| `DataAntemortemPolicy` | tidak punya kolom pencatat → kepemilikan lewat `laporan_kehilangan.dicatat_oleh` |
| `DataPostmortemPolicy` | `diperiksa_oleh` |
| `KecocokanIdentifikasiPolicy` | `diajukan_oleh`; method tambahan `verifikasi` (admin) |
| `DokumenPendukungPolicy` | `diunggah_oleh` |
| `UserPolicy` | tidak menghapus diri sendiri; akun berjejak audit tidak dapat dihapus |

Nama method mengikuti konvensi (`viewAny`, `view`, `create`, `update`, `delete`)
dan dipanggil di controller dengan `Gate::authorize(...)` (bukan
`$this->authorize`, karena `Controller` bawaan Laravel 13 kosong).

## 6. Menu per peran (dibangun server)

`HandleInertiaRequests::share()` menyusun `menu` dari `can('viewAny', ...)` /
Gate. Hasilnya (diuji di `MenuDanKewenanganTest`):

| Peran | Menu |
|---|---|
| admin | Dasbor, Jenazah, Laporan Kehilangan, Kecocokan, Pengguna |
| petugas_forensik | Dasbor, Jenazah, Postmortem, Kecocokan |
| petugas_administrasi | Dasbor, Jenazah, Laporan, Dokumen |
| keluarga_pelapor | Dasbor, Laporan Saya, Data Antemortem Saya |

Label "Laporan Kehilangan / Laporan / Laporan Saya" hanya teks yang berbeda per
peran; keputusan tampil-tidaknya tetap dari Policy. Tombol Ubah, Hapus, dan
Verifikasi di setiap halaman memakai props `can` yang dihitung dengan Policy yang
sama di `app/Http/Resources/*Resource.php`. Props `auth.user` sengaja **tidak**
memuat kolom `role`.

## 7. Keputusan tentang kolom `role`

- Kolom tetap bernama `role` (tidak di-rename menjadi `peran`): sudah dipakai
  model, seeder, dan dokumen; rename hanya menambah risiko.
- Batasan nilai dibuat dengan **migrasi baru**
  `2026_10_19_223438_tambah_batasan_role_pada_users_table.php` berupa
  `CHECK (role IN (...))`, bukan enum native PostgreSQL (pertemuan 3).
- Nilai bawaan diubah ke peran terendah `keluarga_pelapor`; registrasi publik
  juga memaksa peran ini di `CreateNewUser`. Akun petugas hanya dibuat admin.
- Tidak memakai paket peran dari luar (Spatie dan sejenisnya).

## 8. Kejanggalan pada matriks awal (asumsi README) dan keputusan yang diambil

| # | Kejanggalan | Keputusan sementara |
|---|---|---|
| 1 | Registrasi publik starter kit + default kolom `role` = `petugas_administrasi` → siapa pun yang mendaftar menjadi petugas. | Default diubah ke `keluarga_pelapor` (migrasi baru) dan `CreateNewUser` memaksa peran ini. |
| 2 | Petugas forensik harus "mengajukan kecocokan" tetapi tidak boleh melihat laporan/antemortem, padahal pencocokan membutuhkan keduanya. | Forensik boleh **melihat** rincian laporan & antemortem (tanpa menu, tanpa ubah). |
| 3 | Petugas administrasi "CRUD jenazah" termasuk kolom `status_identifikasi` → bisa menandai teridentifikasi tanpa verifikasi admin. | Status tidak ada di formulir dan tidak `$fillable`; hanya berubah lewat pengajuan (→ `proses`) dan verifikasi admin (→ `teridentifikasi`). |
| 4 | Menghapus jenazah ikut menghapus (cascade) postmortem dan kecocokan, yaitu barang bukti forensik. | Administrasi hanya boleh menghapus jenazah yang belum diperiksa; admin boleh semua. |
| 5 | Keluarga "mengubah laporannya" — termasuk `status`? | Keluarga tidak boleh mengirim `status` (`prohibited`) dan laporan terkunci setelah `cocok`/`ditutup`. |
| 6 | Siapa mengisi antemortem untuk laporan yang dicatat petugas? Matriks tidak menyebut. | Admin, administrasi, dan pemilik laporan. |
| 7 | Tabel kecocokan tidak mencatat pengaju → tidak ada jejak audit dan kepemilikan usulan. | Migrasi baru kolom `diajukan_oleh` (nullable, FK RESTRICT). |
| 8 | Admin "semua modul" tetapi menu admin tidak memuat Postmortem/Dokumen/Antemortem. | Menu mengikuti daftar yang diminta; admin membuka data itu dari halaman jenazah/laporan. Daftar mandirinya 403 bagi admin. |
| 9 | Admin memverifikasi; bila admin juga boleh mengajukan, ia memverifikasi usulannya sendiri. | Pengajuan hanya oleh petugas forensik (pemisahan tugas). |
| 10 | Akun petugas yang dihapus melanggar FK RESTRICT (jejak audit). | `UserPolicy::delete` menolak akun yang punya jejak audit; tombol Hapus tidak tampil. |
