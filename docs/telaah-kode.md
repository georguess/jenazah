# Telaah Kode ala UTS (dilakukan sebelum diserahkan)

Daftar kesalahan yang **berjalan tanpa galat tetapi salah** — ditemukan saat
menelaah hasil kerja sendiri — beserta perbaikannya.

| # | Temuan (berjalan, tetapi salah) | Aturan yang dilanggar | Perbaikan |
|---|---|---|---|
| 1 | `jenazah.status_identifikasi` varchar(20) dengan nilai bawaan `belum_teridentifikasi` (21 huruf): setiap INSERT tanpa status gagal. Tak terlihat karena seeder lama selalu mengisi status. | P3 skema adalah kode | Migrasi **baru** memperlebar kolom ke 30; migrasi lama tidak disentuh. |
| 2 | Registrasi publik starter kit + default `role` = `petugas_administrasi` → siapa pun yang mendaftar menjadi petugas. | P7 otorisasi | Default via migrasi baru → `keluarga_pelapor`; `CreateNewUser` memaksa peran itu; diuji. |
| 3 | `status_identifikasi`, `status_verifikasi`, `diverifikasi_oleh` ada di `$fillable` → bisa disusupkan lewat formulir ubah. | P3 `$fillable`, P7 | Dikeluarkan dari `$fillable`; hanya berubah lewat `KecocokanIdentifikasi::ajukan()/verifikasi()`; diuji (`status_identifikasi` yang disusupkan tidak tersimpan). |
| 4 | Setelah #3, seeder diam-diam kehilangan nilai status (mass assignment mengabaikan kolom). | P3 seeder | Seeder memakai `forceFill` untuk kolom tersebut. |
| 5 | `Route::resource('dokumen')` menghasilkan parameter `{dokuman}` (singularisasi bahasa Inggris) → binding tidak cocok dengan `$dokumen`. | P2/P4 route model binding | `->parameters(['dokumen' => 'dokumen'])`. |
| 6 | Satu sumber daya dipecah ke beberapa kelompok peran → `/laporan/create` bisa tertangkap `/laporan/{laporan}`. | P2 rute | `Route::patterns([...'[0-9]+'])` di `routes/web.php`. |
| 7 | Pemformat Pint ikut merapikan migrasi lama. | P3 jangan menyunting migrasi lama | Dikembalikan dengan `git checkout`; migrasi lama dimasukkan `notPath` di `pint.json`. |
| 8 | `key={index}` pada breadcrumbs dan daftar galat bawaan starter kit. | P5/P6 key | Diganti kunci stabil. |
| 9 | Setelah Wayfinder dibuang, tautan Keluar kehilangan `method="post"` → GET `/logout` = 405. | P5 Link | `method="post" as="button"`. |
| 10 | Pemeriksaan peran (`punyaPeran`) di controller laporan untuk isian bawaan. | P7 aturan tidak tersebar di controller | Dihapus; semua keputusan peran ada di Policy/middleware. |
| 11 | `config/app.php` zona waktu UTC → aturan `before_or_equal:today` meleset 7 jam di WIB. | P4 validasi | `APP_TIMEZONE=Asia/Jakarta`. |
| 12 | Daftar dengan `can` per baris memanggil Policy yang men-query relasi per baris (N+1). | P3 `with()` | `withExists()` di kueri daftar + `Model::preventLazyLoading()` di luar produksi; diuji. |
| 13 | Build mengunduh font dari internet (`fonts.bunny.net`) → `npm run build` gagal tanpa koneksi. | P3 uji laptop kosong | Unduhan font dibuang; memakai font sistem. |
| 14 | `config/inertia.php` mengaktifkan SSR padahal tidak ada server SSR. | P5 | `ssr.enabled = false`. |

## Batasan yang disadari (belum diubah, perlu keputusan kelompok)

- Form Request berjalan **sebelum** `Gate::authorize` di controller. Pengguna yang
  lolos middleware peran tetapi tidak berhak atas data tertentu (mis. forensik
  mengubah usulan forensik lain) dengan isian tidak sah akan menerima galat
  validasi, bukan 403. Data tetap aman (tidak tersimpan).
- Modul Dokumen tidak punya aksi ubah (berkas bukti diganti dengan unggah ulang
  + hapus). Tujuh aksi lengkap ada pada modul utama (Jenazah) dan modul lain.
- Unduhan berkas memakai `<a>` (bukan `<Link>`) karena respons berupa berkas,
  bukan halaman Inertia.
- Halaman autentikasi bawaan starter kit memakai komponen `<Form>` Inertia 3;
  seluruh formulir modul memakai `useForm`.
- CASCADE di basis data tidak menghapus berkas fisik dokumen di `storage/app/private`.
