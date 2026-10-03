# Pilihan Arsitektur

**Dipilih: Monolit Laravel Blade** (bukan API terpisah+SPA, bukan monolit Inertia)

## Dua alasan teknis
1. **Kompleksitas paling rendah untuk tim kecil/waktu terbatas** — tidak perlu
   membangun & menjaga dua basis kode (backend API + frontend SPA) atau
   menangani auth berbasis token/CORS terpisah; satu request langsung
   ter-render jadi HTML.
2. **Alur data linear cocok dengan sifat aplikasi** — sebagian besar layar
   adalah form pencatatan dan tabel (jenazah, laporan, kecocokan) yang tidak
   butuh interaktivitas real-time tinggi, sehingga overhead SPA/Inertia tidak
   memberi banyak keuntungan dibanding biaya setupnya.

## Satu hal yang dikorbankan
Interaktivitas antarmuka lebih terbatas dibanding Inertia/SPA — perpindahan
halaman melakukan full page reload, dan untuk bagian yang butuh update
sebagian (misalnya form pencocokan dinamis) perlu tambahan library kecil
(Alpine.js) di beberapa titik, bukan didapat otomatis dari arsitekturnya.
