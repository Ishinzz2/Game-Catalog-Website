# Panduan Pengembangan

## Pola kode
- Tempatkan model dan relasi di `app/Models`.
- Tempatkan interaksi reaktif Livewire di `app/Livewire`.
- Simpan halaman dan komponen presentasi pada `resources/views`.
- Gunakan migration untuk setiap perubahan skema; jangan mengubah struktur database secara manual.
- Tambahkan data master/dummy secara idempoten melalui `firstOrCreate` atau `updateOrCreate` di seeder.
- Gunakan eager loading `with()` ketika menampilkan relasi game, platform, kategori, dan riwayat bermain.
- Setiap aksi yang mengubah data inventaris harus memeriksa kepemilikan berdasarkan pengguna terautentikasi.

## Alur pengembangan lokal
1. Buat branch baru dengan nama yang menjelaskan fitur.
2. Jalankan `composer install` dan `npm install`.
3. Siapkan `.env` dan database lokal.
4. Jalankan `php artisan migrate --seed`.
5. Jalankan `php artisan serve` dan `npm run dev`.
6. Periksa perubahan dengan `php artisan route:list`, `php artisan migrate:status`, dan `./vendor/bin/pint --test`.
7. Tulis commit dengan Conventional Commits, misalnya `feat: menambahkan filter platform`.

## Area pengembangan lanjutan
- Integrasi API resmi platform dan sinkronisasi dengan persetujuan pengguna.
- UI CRUD akun platform, registrasi, reset kata sandi, dan verifikasi email.
- Registrasi, reset kata sandi, serta verifikasi email.
- Test feature untuk otorisasi, validasi, CRUD, agregasi statistik, dan deteksi kepemilikan ganda.
- Gunakan pipeline Vite dan Tailwind CSS 4 untuk development maupun build produksi; jalankan `npm run build` sebelum deployment.
