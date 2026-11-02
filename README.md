# GameVault — Game Catalog & Library

GameVault adalah aplikasi web untuk menjelajahi katalog game dan mengelola koleksi game pribadi lintas platform. Aplikasi menyediakan area pengguna dan panel administrator. Data inventaris, aktivitas bermain, wishlist, serta akun platform disimpan per pengguna.

## Fitur saat ini

### Katalog publik
- Menjelajahi katalog game dengan pencarian judul dan pagination.
- Melihat detail game, deskripsi, tanggal rilis, skor Metacritic, rating ESRB, publisher, developer, kategori, dan tag yang tersedia.
- Katalog master mencakup game, publisher, developer, kategori, tag, dan platform.
- Rating ESRB pada form admin dipilih dari opsi RP, EC, E, E10+, T, M, dan AO; nilainya opsional.

### Akun dan dashboard pengguna
- Registrasi, login, dan logout.
- Dashboard pribadi dengan ringkasan jumlah item inventaris, total playtime, estimasi nilai pembelian, dan jumlah game berstatus selesai.
- Preferensi tema dan mata uang disimpan pada profil pengguna.

### Inventaris dan akun platform
- Mengelola akun platform milik pengguna, termasuk platform, username, dan ID eksternal.
- CRUD item inventaris: game, akun platform, format kepemilikan, tipe perolehan, harga/tanggal pembelian, dan status instalasi.
- Status game: Unplayed, Backlog, Playing, Completed, 100% Platinum, dan Dropped.
- Rating pengguna, catatan ulasan, dan waktu terakhir dimainkan.
- Pencarian, filter platform/status/format/genre/prioritas backlog/instalasi/tipe perolehan, pengurutan, dan pagination.
- Data inventaris dan akun platform dibatasi berdasarkan pengguna yang sedang login.

### Aktivitas bermain dan backlog
- Mencatat sesi bermain berdasarkan waktu mulai dan selesai; durasi dihitung dari selisih waktu.
- Total playtime menggabungkan baseline/manual playtime dengan durasi sesi yang tercatat.
- Grafik aktivitas bermain untuk rentang tujuh hari.
- Menghapus sesi akan menghitung ulang total playtime dan waktu terakhir bermain.
- Mencatat serta menghapus achievement secara manual per item inventaris.
- Backlog memiliki prioritas, target tanggal selesai, catatan, dan filter prioritas.

### Wishlist dan rekomendasi
- CRUD wishlist dengan target platform opsional, harga maksimal, prioritas, dan catatan.
- Mencatat dan menghapus riwayat harga secara manual, termasuk mata uang, sumber, dan tanggal pencatatan.
- Memindahkan item wishlist ke inventaris dengan memilih akun platform.
- Rekomendasi game berdasarkan kesamaan kategori dengan game yang sudah dimiliki pengguna.

### Panel administrator
- Role pengguna: admin dan user; halaman admin dilindungi middleware auth dan admin.
- Dashboard admin menampilkan jumlah pengguna, game katalog, platform, dan akun platform pengguna.
- CRUD katalog game, kategori, dan platform.
- Pengelolaan pengguna: mengubah role, soft delete/nonaktifkan, dan memulihkan akun.
- Proteksi mencegah admin mengubah atau menghapus akun sendiri serta menonaktifkan/menghapus administrator aktif terakhir.
- Perubahan data katalog divalidasi di server. Penghapusan platform dicegah jika masih direferensikan oleh akun pengguna.

## Teknologi dan prasyarat

- PHP 8.3 atau lebih baru (constraint Composer: ^8.3).
- Composer 2.
- Node.js dan npm yang kompatibel dengan Vite 7.
- Database yang didukung konfigurasi Laravel; contoh lingkungan proyek menggunakan MySQL.
- Laravel 13, Livewire 4, Blade, Tailwind CSS 4, dan Vite 7.

Versi dependensi PHP dan JavaScript yang diminta proyek tercantum pada composer.json, composer.lock, package.json, dan package-lock.json.

## Instalasi lokal

1. Klon repositori:

       git clone https://github.com/Ishinzz2/Game-Catalog-Website.git
       cd Game-Catalog-Website

2. Pasang dependensi:

       composer install
       npm install

3. Siapkan file environment dan application key:

       cp .env.example .env
       php artisan key:generate

   Pada Windows Command Prompt, salin file dengan perintah copy .env.example .env.

4. Buat database lokal, lalu sesuaikan konfigurasi DB_* di .env.
5. Jalankan migrasi dan seeder:

       php artisan migrate --seed

6. Bangun aset frontend:

       npm run build

   Perintah tersebut menghasilkan manifest Vite yang digunakan oleh directive @vite. Hasil build berada di public/build dan tidak perlu dibuat secara manual.

7. Jalankan aplikasi:

       php artisan serve

   Untuk pengembangan frontend dengan hot reload, jalankan npm run dev di terminal kedua dan biarkan proses Vite tetap aktif.

## Akun demo seeder

Seeder membuat akun berikut untuk pengujian lokal:

| Role | Email | Password |
|---|---|---|
| User | demo@gamevault.test | password |
| Admin | admin@gamevault.test | password |

Kredensial tersebut hanya untuk lingkungan lokal/demo. Ganti atau hapus akun dan password contoh sebelum aplikasi digunakan di lingkungan nyata.

## Pengujian dan pemeriksaan

Konfigurasi PHPUnit menggunakan SQLite in-memory untuk pengujian. Setelah dependensi terpasang, jalankan:

    php artisan test

Pemeriksaan tambahan yang relevan:

    php artisan route:list
    php artisan migrate:status
    ./vendor/bin/pint --test
    npm run build

## Troubleshooting Vite

Jika muncul ViteManifestNotFoundException atau pesan Vite manifest not found, pastikan dependensi Node sudah terpasang lalu jalankan npm run build. Jika menggunakan mode development, pastikan npm run dev masih berjalan. Jangan membuat public/build/manifest.json secara manual.

## Struktur database

Skema dan relasi data dijelaskan lebih rinci pada [Dokumentasi ERD](docs/ERD.md). Secara garis besar, tabel utama meliputi:

| Tabel | Kegunaan |
|---|---|
| users | Akun, role, preferensi tema, mata uang |
| publishers, developers | Data penerbit dan pengembang |
| categories, tags | Kategori hierarkis dan tag |
| platforms, user_platform_accounts | Master platform dan akun platform milik pengguna |
| games | Katalog game, termasuk skor Metacritic dan rating ESRB |
| category_game, game_tag | Relasi many-to-many katalog |
| user_game_inventories | Item kepemilikan game pengguna dan metadata backlog |
| game_play_sessions | Ringkasan status dan total playtime inventaris |
| play_activity_sessions | Log sesi bermain individual |
| game_achievements | Achievement yang dicatat pengguna |
| wishlists | Game incaran pengguna |
| wishlist_price_histories | Riwayat harga wishlist yang dicatat manual |

## Struktur kode penting

- app/Models: model dan relasi Eloquent.
- app/Livewire: interaksi reaktif untuk autentikasi, inventaris, akun platform, aktivitas, wishlist, dan panel admin.
- app/Http/Middleware: middleware otorisasi administrator.
- database/migrations: evolusi skema database.
- database/factories dan database/seeders: data pengujian dan data awal.
- resources/views: halaman Blade, layout, dan tampilan Livewire.
- routes/web.php: route publik, pengguna terautentikasi, dan administrator.
- docs/: dokumentasi ERD dan panduan pengembangan.

## Batasan dan hal yang belum tersedia

- Belum ada sinkronisasi API otomatis dengan Steam, PlayStation Network, Xbox, Nintendo, GOG, atau Epic Games.
- Status koneksi akun platform dan ID eksternal disimpan, tetapi integrasi/sinkronisasi platform belum diimplementasikan.
- Achievement dan riwayat harga dicatat manual; tidak ada pengambilan otomatis dari layanan eksternal atau pemantauan harga otomatis.
- Rekomendasi menggunakan kesamaan kategori katalog, bukan model rekomendasi eksternal.
- Fitur ini merupakan aplikasi pengelolaan katalog dan koleksi; bukan pengganti layanan akun/platform resmi.

Periksa konfigurasi, jalankan migrasi, pengujian, dan audit keamanan di lingkungan target sebelum deployment.

## Panduan kontribusi

Ikuti [Panduan Pengembangan](docs/PANDUAN-PENGEMBANGAN.md). Gunakan branch terpisah untuk pekerjaan, tambahkan migration untuk perubahan skema, dan gunakan Conventional Commits seperti feat:, fix:, refactor:, chore:, atau docs:.
