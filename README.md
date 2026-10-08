<div align="center">

# 🎪 DYACARA — Event Organizer Platform

<p align="center">
  <strong>Mewujudkan Event Impian Anda dengan Sentuhan Profesional</strong>
</p>

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Filament Version](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=livewire&logoColor=white)](https://filamentphp.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Tests Passing](https://img.shields.io/badge/Tests-44%20Passed-10B981?style=for-the-badge&logo=checkmarx&logoColor=white)](https://pestphp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

---

</div>

## 📖 Tentang Dyacara

**DYACARA** adalah platform web modern untuk layanan *Event Organizer* (EO) yang mengintegrasikan pengalaman interaktif bagi klien dan panel kontrol terpadu bagi administrator. Mulai dari pemesanan acara pernikahan (*Wedding Planning*), *Family Gathering*, *Birthday Party*, hingga *Corporate Engagement*, Dyacara mendigitalkan alur reservasi, simulasi pembayaran uang muka (DP 10%), verifikasi bukti pembayaran, hingga notifikasi otomatis via email.

---

## ✨ Fitur Utama

### 👤 Portal Klien (Customer Facing)
- **Katalog & Pencarian Event**: Filter event berdasarkan kategori (*engagement*, *gathering*, *birthday*), lokasi, tanggal, dan kata kunci pencarian.
- **Pemesanan Acara (Booking System)**: Formulir pemesanan langsung terhubung ke akun klien dengan validasi pencegahan duplikasi reservasi aktif.
- **Simulasi & Pembayaran DP 10%**: Perhitungan uang muka otomatis mengacu pada tarif resmi katalog layanan (*Service*).
- **Upload Bukti Pembayaran Aman**: Unggah bukti transfer bank atau e-wallet ke penyimpanan privat terisolasi.
- **Riwayat & Status Transaksi**: Dashboard pemesanan dan pembayaran pribadi dengan status terkini (*Menunggu Konfirmasi*, *Dikonfirmasi*, *Terverifikasi*, *Ditolak*).
- **Notifikasi Email Otomatis**: Email otomatis dikirim saat pemesanan atau bukti transfer diverifikasi/ditolak oleh admin.
- **Autentikasi Lengkap**: Registrasi, Login, Forgot Password, Reset Password, Konfirmasi Password, Verifikasi Email, dan Pengaturan Profil.

### 🛡️ Dashboard Administrator (Filament v3)
- **Ringkasan & Statistik**: Monitoring performa operasional acara dan pendapatan secara *real-time*.
- **Manajemen Event**: Kelola event (Upcoming, Ongoing, Completed) dengan visual status badge dinamis.
- **Manajemen Layanan (Services)**: Atur paket acara, deskripsi, harga resmi, serta status aktifasi layanan.
- **Verifikasi Pemesanan**: Konfirmasi atau tolak permintaan booking klien dalam 1 klik dengan trigger email notifikasi otomatis.
- **Verifikasi Pembayaran & Bukti Transfer**: Akses langsung dokumen bukti pembayaran privat dengan aksi *Verify* atau *Reject*.
- **Pesan Kontak & Galeri**: Inbox pesan masuk dari pengunjung website serta galeri dokumentasi acara.
- **Manajemen Pengguna**: Kontrol akses pengguna dan hak istimewa administrator.

---

## 🛠️ Tech Stack & Arsitektur

| Komponen | Teknologi |
| :--- | :--- |
| **Backend Framework** | Laravel 12.x |
| **Bahasa Pemrograman** | PHP 8.2+ |
| **Admin Panel** | Filament v3 (Livewire 3, Alpine.js) |
| **Frontend** | Blade Templates, Bootstrap 5.3, FontAwesome 6 |
| **Database** | MySQL (MariaDB compatible) / SQLite untuk testing |
| **Storage Engine** | Local Storage (Private & Public) / AWS S3 Compatible |
| **Mailing System** | Laravel Mailable (SMTP / Log driver) |
| **Test Suite** | Pest PHP & PHPUnit (44 Automated Feature & Unit Tests) |

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
Pastikan telah menginstal:
- PHP >= 8.2 (dengan ekstensi: `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `curl`)
- Composer >= 2.x
- Node.js & NPM
- MySQL / MariaDB

### 2. Kloning Repositori
```bash
git clone https://github.com/Abysmaa/Dyacara_web.git
cd Dyacara_web
```

### 3. Instal Dependensi
```bash
composer install
npm install
```

### 4. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi lingkungan:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dyacara_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seed Database
Jalankan migrasi database beserta data awal (seeder admin, services, dan event):
```bash
php artisan migrate --seed
```

### 6. Tautkan Storage Link
```bash
php artisan storage:link
```

### 7. Jalankan Server
Buka dua terminal terpisah:

**Terminal 1 (Laravel Server):**
```bash
php artisan serve
```

**Terminal 2 (Asset Bundler):**
```bash
npm run dev
```

Aplikasi siap diakses di:
- **Website Publik**: [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Admin Panel**: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## 🔑 Kredensial Administrator Bawaan

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `dyacara@admin.com` | `admin123` |
| **Administrator** | `admin@dyacara.com` | `admin123` |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Proyek ini dilengkapi dengan cakupan tes otomatis yang menyeluruh untuk alur autentikasi, otorisasi hak akses, keamanan upload, kalkulasi pembayaran, hingga aksi Filament Admin.

Jalankan seluruh tes dengan perintah:
```bash
php artisan test
```

Hasil uji:
```text
  PASS  Tests\Unit\ExampleTest
  PASS  Tests\Feature\Auth\AuthenticationTest
  PASS  Tests\Feature\Auth\EmailVerificationTest
  PASS  Tests\Feature\Auth\PasswordConfirmationTest
  PASS  Tests\Feature\Auth\PasswordResetTest
  PASS  Tests\Feature\Auth\PasswordUpdateTest
  PASS  Tests\Feature\Auth\RegistrationTest
  PASS  Tests\Feature\EventAndPaymentFlowTest
  PASS  Tests\Feature\ExampleTest
  PASS  Tests\Feature\ProfileTest

  Tests:    44 passed (155 assertions)
  Duration: 4.16s
```

---

## 🔒 Keamanan & Kebijakan File Bukti Pembayaran

- **Private Filesystem Disk**: Bukti pembayaran disimpan pada disk privat (`PAYMENT_PROOFS_DISK=local` pada mode dev, atau `s3` pada mode produksi).
- **Route Authorization Gate**: Bukti pembayaran tidak dapat diakses secara publik; hanya admin terautentikasi yang memiliki izin membuka tautan bukti bayar (`route('admin.payments.proof')`).
- **Validasi Nilai DP**: Nilai deposit 10% dihitung dari sisi server (*server-side calculation*) berdasarkan tarif layanan resmi, mencegah manipulasi harga dari form input sisi klien.

---

## 📁 Struktur Direktori Penting

```plaintext
app/
├── Enums/                     # PHP 8.1+ Enums (BookingStatus, EventStatus, PaymentStatus)
├── Filament/Resources/        # Panel Admin Filament (Event, Booking, Payment, Service, User)
├── Http/Controllers/          # Web Controllers (Event, Booking, Payment, Profile, Auth)
├── Mail/                      # Mailable Notifications (Booking & Payment Status)
└── Models/                    # Eloquent Models (User, Event, Service, Booking, Payment)
database/
├── migrations/                # Database Migrations terstruktur
└── seeders/                   # Initial Data Seeders
resources/
├── views/                     # Blade Templates (Website responsif & Email templates)
└── css/ js/                   # Styling & Scripts
tests/
└── Feature/                   # Feature Tests (Event, Booking, Payment flow, Auth)
```

---

## 📄 Lisensi

Proyek ini berada di bawah lisensi [MIT License](LICENSE).

<div align="center">
  <p>Dibuat dengan ❤️ untuk <strong>Dyacara Event Organizer</strong></p>
</div>
