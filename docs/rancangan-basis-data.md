# Rancangan Skema Basis Data

## 1. users
| Atribut | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name, email | string | email unik |
| password | string (hashed) | |
| role | string(30) | admin/petugas_forensik/petugas_administrasi/keluarga_pelapor. Dibuat string, bukan enum PostgreSQL native, agar penambahan role baru cukup lewat validasi aplikasi tanpa `ALTER TYPE`. |

## 2. jenazah
| Atribut | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kode_jenazah | string(30) UNIQUE | identitas fisik/label di lapangan; unik agar tidak ada duplikasi barang bukti |
| tanggal_ditemukan | date | |
| status_identifikasi | string(20) | disimpan eksplisit (bukan diturunkan dari join ke `kecocokan_identifikasi`) agar query status cepat |
| dicatat_oleh | FK → users, **RESTRICT** | user pencatat tidak boleh dihapus selama masih menjadi pencatat data (audit trail) |

## 3. laporan_kehilangan
| Atribut | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| dicatat_oleh | FK → users, nullable, **SET NULL** | laporan bisa datang dari keluarga tanpa akun; bukti tidak boleh ikut hilang jika akun petugas dihapus |
| status | string(20) | aktif/cocok/ditutup |

## 4. data_antemortem (relasi 1–1 ke laporan_kehilangan)
| Atribut | Tipe | Keterangan |
|---|---|---|
| laporan_kehilangan_id | FK UNIQUE, **CASCADE** | unique memaksa 1-ke-1; cascade karena data ini tidak bermakna tanpa laporannya |
| tinggi_badan_cm, berat_badan_kg | decimal(5,1) | decimal dipakai (bukan float) agar nilai ukur tidak mengalami galat pembulatan biner saat dibandingkan ke data postmortem |

## 5. data_postmortem (relasi 1–1 ke jenazah)
| Atribut | Tipe | Keterangan |
|---|---|---|
| jenazah_id | FK UNIQUE, **CASCADE** | sama seperti antemortem |
| diperiksa_oleh | FK → users, **RESTRICT** | dokter forensik pemeriksa adalah bukti hukum (chain of custody), tidak boleh hilang meski akun dihapus |

## 6. kecocokan_identifikasi (relasi many-to-many jenazah ↔ laporan_kehilangan dengan atribut)
| Atribut | Tipe | Keterangan |
|---|---|---|
| jenazah_id, laporan_kehilangan_id | FK, **CASCADE** | |
| skor_kecocokan | decimal(5,2) | decimal, bukan float, agar skor 0–100 konsisten saat diurutkan/dibandingkan |
| UNIQUE(jenazah_id, laporan_kehilangan_id, metode_kecocokan) | | mencegah pencatatan ganda untuk metode yang sama pada pasangan yang sama |

Dibuat sebagai tabel eksplisit (bukan pivot polos) karena relasi ini punya
atribut sendiri (skor, metode, status verifikasi) dan sifatnya many-to-many:
satu jenazah bisa diuji cocok dengan banyak laporan, dan sebaliknya.

## 7. dokumen_pendukung
| Atribut | Tipe | Keterangan |
|---|---|---|
| jenazah_id, laporan_kehilangan_id | FK nullable, **CASCADE** | |
| — | CHECK `num_nonnulls(jenazah_id, laporan_kehilangan_id) = 1` | memastikan setiap dokumen tertaut ke tepat satu induk (bukan dua-duanya, bukan kosong dua-duanya) |
| diunggah_oleh | FK → users, **RESTRICT** | jejak siapa yang mengunggah bukti tidak boleh hilang |

## Diagram relasi (ringkas)
```
users 1---N jenazah
users 1---N laporan_kehilangan
laporan_kehilangan 1---1 data_antemortem
jenazah 1---1 data_postmortem
jenazah N---N laporan_kehilangan  (lewat kecocokan_identifikasi)
jenazah 1---N dokumen_pendukung
laporan_kehilangan 1---N dokumen_pendukung
```

## Perubahan skema setelah dikunci (lewat migrasi BARU)

Migrasi lama tidak disunting. Setiap perubahan dibuat sebagai migrasi baru beserta alasannya:

| Migrasi | Perubahan | Alasan |
|---|---|---|
| `2026_10_19_223438_tambah_batasan_role_pada_users_table` | `CHECK (role IN ('admin','petugas_forensik','petugas_administrasi','keluarga_pelapor'))`; default `role` menjadi `keluarga_pelapor` | Membatasi nilai tanpa enum native; default lama (`petugas_administrasi`) membuat akun registrasi publik otomatis menjadi petugas. Kolom tetap bernama `role`. |
| `2026_10_19_223439_tambah_diajukan_oleh_pada_kecocokan_identifikasi_table` | kolom `diajukan_oleh` FK → users, nullable, **RESTRICT** | Tabel hanya mencatat verifikator, bukan pengaju. Dibutuhkan untuk jejak audit dan Policy "forensik hanya mengubah usulannya sendiri". Nullable untuk baris lama. |
| `2026_10_19_224324_perlebar_status_identifikasi_pada_jenazah_table` | `jenazah.status_identifikasi` varchar(20) → varchar(30) | **Cacat migrasi lama**: nilai bawaan `belum_teridentifikasi` panjangnya 21 karakter sehingga setiap INSERT yang mengandalkan nilai bawaan gagal (`value too long`). Tidak terlihat karena kode lama belum pernah dijalankan dan seeder lama selalu mengisi status. |

### Nilai sah kolom string (dijaga Form Request `in:` dan konstanta model)

| Kolom | Nilai |
|---|---|
| `jenazah.jenis_kelamin`, `laporan_kehilangan.jenis_kelamin` | `laki-laki`, `perempuan`, `tidak_diketahui` |
| `jenazah.kondisi_jenazah` | `utuh`, `sebagian`, `tidak_dikenali` |
| `jenazah.status_identifikasi` | `belum_teridentifikasi` → `proses` (saat diajukan) → `teridentifikasi` (saat disetujui admin) |
| `laporan_kehilangan.status` | `aktif`, `cocok` (hanya dari verifikasi), `ditutup` |
| `kecocokan_identifikasi.metode_kecocokan` | `gigi`, `sidik_jari`, `dna`, `ciri_khusus`, `properti` |
| `kecocokan_identifikasi.status_verifikasi` | `menunggu`, `disetujui`, `ditolak` |
| `dokumen_pendukung.jenis_dokumen` | `foto`, `sidik_jari`, `surat_keterangan`, `berita_acara`, `identitas`, `lainnya` |
| `*.golongan_darah` | `A`, `B`, `AB`, `O` |

### Catatan

- Berkas dokumen disimpan di disk privat `storage/app/private/dokumen-pendukung`
  dan hanya dapat diunduh lewat `dokumen.unduh` (melewati Policy).
- Penghapusan berantai (CASCADE) di basis data tidak menghapus berkas fisik
  dokumen; berkas hanya ikut terhapus bila dokumen dihapus lewat aplikasi.
- Pengujian memakai PostgreSQL (`si_manajemen_jenazah_test`) karena CHECK
  `num_nonnulls()` tidak ada di SQLite.
