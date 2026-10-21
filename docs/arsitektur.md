# Pilihan Arsitektur

**Dipilih: Monolit Inertia + React** (Laravel 13 + Inertia 3 + React, JavaScript/JSX).

Revisi dari pilihan awal "monolit Blade". Rute, controller, model, validasi, dan
otorisasi tetap milik Laravel; hanya lapisan tampilan yang berganti dari Blade ke
komponen React. Satu-satunya berkas Blade yang tersisa adalah
`resources/views/app.blade.php` (kerangka HTML yang memuat React).

## Tiga bentuk yang dipertimbangkan

| | Monolit Blade | **Monolit Inertia + React** | API terpisah + SPA |
|---|---|---|---|
| Yang menyusun tampilan | Server (HTML jadi) | React di peramban, data dari controller sebagai props | React di peramban, data dari API JSON |
| Rute & otorisasi | Satu tempat | **Satu tempat** (server) | Dua tempat (API + klien) |
| Autentikasi | Sesi | **Sesi (Fortify, starter kit)** | Token/CORS |
| Perpindahan halaman | Muat ulang penuh | Tukar komponen, tanpa muat ulang | Tukar komponen |

## Dua alasan teknis

1. **Antarmuka interaktif tanpa API kedua.** Alur identifikasi membutuhkan
   formulir dengan umpan balik langsung (tombol sibuk, galat per isian tanpa
   halaman berkedip, konfirmasi hapus, perbandingan antemortem–postmortem
   berdampingan). Inertia memberi semua itu sambil tetap memakai controller,
   Form Request, dan Policy yang sama — tidak ada rute API ganda dan aturan
   otorisasi tidak tersalin ke dua tempat.
2. **Autentikasi dan tata letak dari starter kit resmi.** Starter kit React
   Laravel 13 sudah menyediakan login, registrasi, lupa sandi, verifikasi email
   (Fortify), sesi, dan tata letak bilah samping yang teruji. Tim tidak menulis
   kode autentikasi sendiri (pertemuan 7).

## Yang dikorbankan (trade-off)

- **Dua bahasa dan satu langkah build.** Anggota tim harus menguasai PHP dan
  JSX, dan berkas antarmuka wajib dibangun (`npm run dev`/`npm run build`).
  Lupa menjalankannya menghasilkan halaman kosong tanpa pesan galat.
- **Props terlihat di peramban.** Semua props bisa dibaca lewat alat
  pengembang, jadi data harus disaring di kueri server (bukan di React), dan
  tidak boleh ada rahasia di props. Proyek ini mengirim data lewat
  `JsonResource` agar hanya kolom yang perlu yang terkirim.
- **Tidak cocok untuk banyak klien.** Bila kelak ada aplikasi seluler, tetap
  perlu API terpisah.

## Perjalanan satu permintaan

```
Klik <Link> ─► XHR (header X-Inertia) ─► middleware auth, verified, peran
           ─► Controller: Gate::authorize(...) ─► kueri (with, paginate)
           ─► Inertia::render('jenazah/index', props) ─► JSON
           ─► React menukar komponen resources/js/pages/jenazah/index.jsx
Kirim formulir (useForm.post) ─► Form Request ─► simpan ─► redirect + flash
           ─► GET halaman berikutnya (POST–Redirect–GET)
```

## Peta berkas

| Lapisan | Lokasi |
|---|---|
| Rute (dikelompokkan per peran) | `routes/web.php`, `routes/settings.php` |
| Middleware peran | `app/Http/Middleware/PastikanPeran.php` (alias di `bootstrap/app.php`) |
| Props bersama (auth, flash, menu, rute) | `app/Http/Middleware/HandleInertiaRequests.php` |
| Controller (7 aksi, dibuat dengan artisan) | `app/Http/Controllers/*Controller.php` |
| Validasi | `app/Http/Requests/*Request.php` |
| Bentuk data untuk React + `url` + `can` | `app/Http/Resources/*Resource.php` |
| Otorisasi | `app/Policies/*Policy.php`, Gate di `AppServiceProvider` |
| Halaman (huruf kecil, garis miring) | `resources/js/pages/<modul>/{index,show,create,edit}.jsx` |
| Komponen bertanggung jawab tunggal | `resources/js/components/` (Kartu, Kosong, PesanFlash, TombolHapus, Baris*, Form*) |
| Tata letak (dipilih sekali) | `resources/js/app.jsx` → `layouts/` |

## Konvensi yang dijaga

- `Inertia::render('modul/aksi')`; nama berkas halaman huruf kecil semua.
- Tata letak dipilih di `app.jsx`; `Halaman.layout` hanya berisi props tata
  letak (breadcrumbs), bukan komponen (pola Inertia 2 tidak dipakai).
- Semua alamat berasal dari server (`route()` di controller/Resource atau props
  bersama `rute`) — tidak ada URL harfiah di komponen. Wayfinder (pembantu
  TypeScript starter kit) tidak dipakai.
- Paginator dipetakan lewat `data.data.map`, `<Link>` untuk perpindahan,
  `key={id}`, `className`.
- State hanya untuk hal yang lahir di peramban (panel konfirmasi hapus, isian
  yang sedang diketik lewat `useForm`).
