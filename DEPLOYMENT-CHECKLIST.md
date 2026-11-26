# Deployment Checklist — Dyacara

Daftar periksa wajib sebelum men-deploy aplikasi Dyacara ke lingkungan staging atau produksi yang dapat diakses publik.

## 1. Environment & Konfigurasi Dasar

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` (wajib mati agar error 500 tidak membocorkan stack trace, path file, dan variabel lingkungan)
- [ ] `APP_KEY` terisi dengan kunci acak yang valid (`php artisan key:generate`)
- [ ] `APP_URL` menggunakan HTTPS (`https://...`)
- [ ] File `.env` tidak pernah di-commit ke repositori Git (pastikan `.gitignore` aktif)

## 2. Caching & Optimasi Laravel

- [ ] Jalankan `php artisan config:cache`
- [ ] Jalankan `php artisan route:cache`
- [ ] Jalankan `php artisan view:cache`

## 3. Penyimpanan Berkas Privat & Storage Link

- [ ] Pastikan `FILESYSTEM_PAYMENT_PROOFS=local` (atau disk S3 privat) — bukan `public`
- [ ] Pastikan tidak ada file bukti pembayaran di `storage/app/public/payment_proofs/` sebelum menjalankan `php artisan storage:link`
- [ ] Jalankan `php artisan storage:link` hanya setelah memastikan folder `public/` tidak berisi berkas sensitif

## 4. Keamanan PHP & Web Server

- [ ] Atur `expose_php = Off` di `php.ini` produksi agar header `X-Powered-By` tidak dikirim
- [ ] Pastikan HTTPS aktif (HSTS & redirect HTTP -> HTTPS di web server/reverse proxy)
- [ ] Verifikasi `SecurityHeaders` middleware aktif (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`)
- [ ] Uji `Content-Security-Policy` menggunakan mode `Report-Only` terlebih dahulu sebelum mengaktifkan mode enforce
