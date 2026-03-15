# Lootku Market

Prototype marketplace item game berbasis Laravel untuk kebutuhan buyer dan seller.

## Yang sudah dibuat

- Buyer storefront di `/` dengan pola layout mirip marketplace game: header biru besar, search bar, promo card, kategori cepat, dan grid produk.
- Halaman katalog di `/katalog` dengan filter keyword dan tipe produk.
- Halaman detail produk di `/produk/{slug}`.
- Halaman checkout di `/checkout/{slug}` yang membuat transaksi baru, mengurangi stok, dan mengirim order ke history buyer serta dashboard seller.
- Dashboard penjual di `/seller/dashboard` untuk overview omzet, order, listing, dan pergerakan saldo.
- Panel listing seller di `/seller/produk` untuk buat, edit, dan aktif/nonaktifkan produk.
- Login dan registrasi di `/masuk` dan `/daftar` dengan role `buyer` atau `seller`.
- Riwayat transaksi di `/riwayat-transaksi` yang membaca data sesuai user login.
- Update status transaksi seller dari dashboard dan halaman riwayat.
- Data produk dan transaksi sekarang sudah memakai database, bukan array hardcoded.
- Seeder default sekarang membuat akun demo buyer, seller, dan admin untuk testing, tanpa produk atau transaksi dummy.
- Halaman detail transaksi di `/transaksi/{id}` untuk invoice, timeline order, aksi buyer, dan chat buyer-seller.
- Dashboard admin di `/admin/dashboard` untuk monitor akun, listing, stok kritis, seller teratas, dan transaksi terbaru.

## Stack

- Laravel 12
- Blade
- Tailwind CSS 4 via Vite
- SQLite default bawaan Laravel untuk local bootstrap

## Menjalankan proyek

```bash
composer install
npm install
php artisan migrate --seed
php artisan serve
npm run dev
```

Untuk build production:

```bash
npm run build
```

## Verifikasi yang sudah dijalankan

```bash
php artisan migrate --seed
php artisan route:list
php artisan view:cache
php artisan test
npm run build
```

## Akun demo

- Buyer: `buyer@lootku.test` / `password123`
- Seller: `seller@lootku.test` / `password123`
- Admin: `admin@lootku.test` / `password123`

## Langkah lanjut yang paling masuk akal

1. Tambahkan payment gateway nyata seperti Midtrans atau Xendit supaya status `Menunggu Pembayaran` bisa terkonfirmasi otomatis.
2. Tambahkan notifikasi realtime dan dispute/refund flow jika ingin mendekati pola escrow marketplace production.
3. Tambahkan payment gateway nyata seperti Midtrans atau Xendit agar status pembayaran berubah otomatis.
4. Tambahkan social login seperti Google, Steam, dan Discord.
5. Tambahkan file upload bukti pembayaran, lampiran chat, dan moderation tools admin yang lebih lengkap.
