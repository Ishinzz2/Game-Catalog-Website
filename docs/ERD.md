# Dokumentasi ERD GameVault

Dokumen ini menjelaskan hubungan data pada Merged Game Library.

## Kelompok data
1. **Master katalog:** `games`, `publishers`, `developers`, `categories`, dan `tags`.
2. **Platform dan akun:** `platforms` merupakan katalog platform global, sedangkan `user_platform_accounts` menyimpan akun spesifik milik pengguna.
3. **Kepemilikan:** `user_game_inventories` merepresentasikan satu kepemilikan game di satu akun platform. Satu judul dapat dimiliki beberapa kali pada platform berbeda.
4. **Aktivitas:** `game_play_sessions` menyimpan status penyelesaian, waktu bermain, rating, catatan, dan waktu terakhir dimainkan.
5. **Wishlist:** `wishlists` menghubungkan pengguna dengan game incaran dan platform tujuan.

## Relasi
- Satu publisher/developer dapat berhubungan dengan banyak game; referensi di game boleh kosong.
- Kategori mendukung hierarki melalui `categories.parent_id`.
- Game dan kategori serta game dan tag menggunakan relasi many-to-many melalui tabel pivot.
- Pengguna mempunyai banyak akun platform dan item inventaris.
- Akun platform memiliki banyak item inventaris.
- Item inventaris mempunyai riwayat sesi bermain.
- Wishlist terkait dengan pengguna, game, dan platform tujuan opsional.

## Diagram
```mermaid
erDiagram
  USERS ||--o{ USER_PLATFORM_ACCOUNTS : owns
  PLATFORMS ||--o{ USER_PLATFORM_ACCOUNTS : provider
  USERS ||--o{ USER_GAME_INVENTORIES : owns
  GAMES ||--o{ USER_GAME_INVENTORIES : title
  USER_PLATFORM_ACCOUNTS ||--o{ USER_GAME_INVENTORIES : account
  PUBLISHERS ||--o{ GAMES : publishes
  DEVELOPERS ||--o{ GAMES : develops
  GAMES }o--o{ CATEGORIES : genres
  GAMES }o--o{ TAGS : metadata
  USER_GAME_INVENTORIES ||--o{ GAME_PLAY_SESSIONS : activity
  USERS ||--o{ WISHLISTS : creates
  GAMES ||--o{ WISHLISTS : desired
  PLATFORMS ||--o{ WISHLISTS : target
  CATEGORIES ||--o{ CATEGORIES : parent_child
```

## Integritas dan indeks
Foreign key menggunakan cascade untuk data kepemilikan yang bergantung pada pengguna/game, restrict pada penghapusan platform master yang sedang digunakan, dan null-on-delete untuk referensi master opsional. Indeks disediakan untuk judul/tanggal skor, user dan game, akun platform, status bermain, serta prioritas wishlist. Tabel utama menggunakan soft delete jika pemulihan data dibutuhkan.
