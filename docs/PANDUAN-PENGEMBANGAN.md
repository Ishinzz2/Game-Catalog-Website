# Panduan Pengembangan

Dokumen ini menjelaskan pola kerja yang sesuai dengan struktur dan fitur yang tersedia pada repository saat ini.

## Struktur kode

- app/Models: model Eloquent dan relasi database.
- app/Livewire/Auth: komponen login dan registrasi.
- app/Livewire: komponen inventaris, akun platform, aktivitas bermain, dan wishlist.
- app/Livewire/Admin: pengelolaan katalog game, kategori, platform, dan pengguna.
- app/Http/Middleware/EnsureUserIsAdmin.php: middleware pembatas akses administrator.
- database/migrations: perubahan skema database.
- database/factories dan database/seeders: factory dan data awal/demo.
- resources/views: halaman Blade, layout, dan tampilan komponen Livewire.
- routes/web.php: route publik, route pengguna login, dan route admin.
- docs: dokumentasi ERD dan panduan ini.

## Persyaratan dan setup lokal

1. Gunakan PHP 8.3 atau lebih baru sesuai constraint ^8.3 pada composer.json.
2. Pasang Composer 2, Node.js, dan npm yang kompatibel dengan Vite 7.
3. Jalankan composer install dan npm install.
4. Salin .env.example menjadi .env, lalu jalankan php artisan key:generate.
5. Buat database lokal dan sesuaikan konfigurasi koneksi di .env.
6. Jalankan php artisan migrate --seed.
7. Jalankan npm run build untuk membuat aset produksi, atau npm run dev selama pengembangan frontend.
8. Jalankan php artisan serve.

Perintah instalasi lengkap dan akun demo tersedia pada [README](../README.md).

## Pola implementasi

- Buat branch terpisah untuk setiap pekerjaan; jangan mengembangkan fitur langsung pada main.
- Tempatkan logika interaktif pada komponen Livewire sesuai domainnya, dan tampilan pada Blade.
- Gunakan model Eloquent untuk relasi dan query; gunakan eager loading with() ketika menampilkan relasi yang dibutuhkan.
- Gunakan migration untuk setiap perubahan skema. Jangan mengandalkan perubahan database manual yang tidak tercatat.
- Gunakan validasi server-side untuk semua data dari form/aksi Livewire.
- Batasi query dan aksi data pengguna menggunakan ID pengguna yang sedang terautentikasi. Jangan hanya mengandalkan penyembunyian tombol di UI.
- Untuk fungsi administrator, gunakan middleware route admin dan pemeriksaan otorisasi pada komponen Livewire admin.
- Gunakan transaksi database untuk operasi multi-tabel yang harus konsisten.
- Pertahankan perilaku soft delete jika model terkait menggunakannya; periksa relasi dan constraint sebelum menghapus data master.

## Area fitur yang sudah tersedia

- Katalog publik dan detail game.
- Autentikasi login/registrasi.
- Dashboard pengguna, inventaris, dan akun platform.
- Log sesi bermain, ringkasan playtime, backlog, serta pencatatan achievement manual.
- Wishlist, catatan riwayat harga manual, dan rekomendasi berbasis kategori.
- Panel admin untuk katalog game, kategori, platform, dan pengelolaan role/akun pengguna.

## Batasan integrasi

Status akun platform dan kolom waktu sinkronisasi sudah tersedia, tetapi integrasi API dan proses sinkronisasi eksternal belum ada. Achievement dan riwayat harga masih dicatat manual. Jangan mendokumentasikan integrasi otomatis sebagai fitur aktif sebelum implementasi dan pengujian benar-benar tersedia.

## Pemeriksaan sebelum commit

Jalankan pemeriksaan yang relevan dengan perubahan:

    php artisan route:list
    php artisan migrate:status
    php artisan test
    ./vendor/bin/pint --test
    npm run build

Tidak semua perubahan membutuhkan seluruh perintah di atas, tetapi perubahan migration, Livewire, otorisasi, dan frontend perlu diuji pada area yang terdampak.

## Commit dan dokumentasi

Gunakan Conventional Commits, misalnya:
- feat: menambahkan filter backlog
- fix: memperbaiki validasi inventaris
- refactor: merapikan query dashboard
- docs: menyelaraskan dokumentasi fitur

Jika fitur, route, skema database, prasyarat, atau alur setup berubah, perbarui README dan dokumen terkait dalam perubahan yang sama.
