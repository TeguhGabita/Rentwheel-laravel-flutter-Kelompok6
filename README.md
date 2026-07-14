# RentWheel — Sistem Informasi Penyewaan Mobil

Aplikasi web untuk mengelola proses penyewaan mobil berbasis client-server, terdiri dari dashboard web (Laravel) dan aplikasi mobile (Flutter) yang terhubung melalui REST API — mulai dari data armada, data pelanggan, hingga transaksi penyewaan (booking) dan dashboard laporan.

## 📋 Fitur Utama

### Web (Laravel)
- Autentikasi (Login, Register, Logout) — Laravel Breeze
- Manajemen Role & Permission (Admin, User) — Spatie Permission
- Proteksi akses berbasis role (middleware `role:admin` & `role:user`, 403 Forbidden jika dilanggar)
- Navigasi dinamis sesuai role (menu Dashboard untuk admin, menu Beranda/Cari Mobil/Booking Saya/Pembayaran untuk user)
- Dashboard Admin & Beranda User terpisah setelah login
- CRUD Mobil & Kategori Mobil (katalog dengan pencarian & pagination)
- CRUD Booking / Transaksi Penyewaan
- CRUD Pembayaran (otomatis update status booking menjadi "berjalan")
- Dashboard statistik & grafik tren penyewaan
- REST API (JSON) untuk resource Mobil, Booking, Pembayaran

### Mobile App (Flutter)
- Login terhubung ke REST API Laravel
- Penyimpanan token via `shared_preferences`
- Redirect otomatis ke halaman utama setelah login berhasil

## 🛠️ Tech Stack

| Bagian | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.3, MySQL 8 |
| Frontend Web | Blade, Tailwind CSS, Laravel Breeze |
| Auth & Permission | Laravel Sanctum, Spatie Permission |
| Mobile | Flutter, Dart |
| Local Storage | shared_preferences |
| Version Control | Git, GitHub |

## 📁 Struktur Project

```
Rentwheel-laravel-flutter-Kelompok6/
├── backend/      # Backend & Web (Laravel 13)
├── frontend/     # Mobile App (Flutter)
└── README.md
```

## 📌 Progress Terkini

- **Restrukturisasi ERD** — tabel `pelanggans` dihapus, data pelanggan dikonsolidasikan ke tabel `users` dengan pembeda role Admin/User
- **Tampilan Login** — didesain ulang dengan tema RentWheel (aksen amber, layout split panel kiri-kanan)
- **Pemisahan Dashboard Admin & Beranda User** — admin ke `/dashboard`, user ke `/beranda`
- **Proteksi akses berbasis role** — middleware role diterapkan di route terkait
- **Navigasi dinamis sesuai role**
- **Halaman Beranda User** — ringkasan booking aktif, riwayat booking, status pembayaran, menu cepat
- **CRUD Mobil, Booking, Pembayaran (web)** — sudah berjalan
- **BookingSeeder** — seeder dummy data booking terverifikasi berjalan tanpa error
- **REST API dasar (Sanctum)** — endpoint register, login, logout, me, serta resource Mobil/Booking/Pembayaran tersedia
- **Aplikasi Flutter (tahap awal)** — halaman Login terhubung ke REST API

### Masih dalam pengembangan
- Dashboard Admin (CRUD Kategori Mobil, statistik & grafik)
- Form tambah/edit booking dan pembayaran dari sisi web
- Halaman lanjutan di Flutter (daftar mobil, booking, riwayat)

## ⚙️ Instalasi & Menjalankan Project

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- Flutter SDK
- Git

### Laravel (Web)

```bash
# 1. Clone repository
git clone https://github.com/TeguhGabita/Rentwheel-laravel-flutter-Kelompok6.git
cd Rentwheel-laravel-flutter-Kelompok6/backend

# 2. Install dependencies
composer install
npm install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate
```

Edit file `.env` sesuaikan bagian berikut:

```env
APP_NAME=RentWheel
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rentwheel
DB_USERNAME=root
DB_PASSWORD=root
```

```bash
# 5. Buat database 'rentwheel' di MySQL, lalu jalankan migration & seeder
php artisan migrate:fresh --seed

# 6. Build assets
npm run build

# 7. Jalankan server
php artisan serve
# → http://localhost:8000
```

### Flutter (Mobile App)

```bash
# 1. Masuk ke folder Flutter
cd ../frontend

# 2. Install dependencies
flutter pub get

# 3. Sesuaikan base URL di lib/services/api_service.dart
// Emulator Android
static const String baseUrl = 'http://10.0.2.2:8000/api';

// HP fisik / web (sesuaikan dengan IP lokal komputer)
// static const String baseUrl = 'http://<IP-komputer>:8000/api';

# 4. Pastikan Laravel sudah berjalan, lalu jalankan Flutter
flutter run
```

## 👥 Akun Default

| Role | Email | Password |
|---|---|---|
| Admin | admin@rentwheel.test | password |
| User | user@rentwheel.test | password |

## 📸 Screenshot

_Screenshot dapat ditambahkan di sini._

## 🎥 Video Demo & Presentasi

| Tautan | Keterangan |
|---|---|
| ▶️ [https://youtu.be/ejaM3jwEkXcVideo Demo (YouTube)](#) | Demo seluruh fitur web & mobile |
| 📊 [https://www.canva.com/design/DAHPWIO-w0o/wJH9d91z-QT66nxOUQKjTg/editSlide Presentasi (Canva)](#) | Slide presentasi tugas besar |

## 📄 Laporan

Dokumen laporan ilmiah tersedia di folder Laporan/.
## 🗄️ API Endpoints

### Auth

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| POST | /api/register | Registrasi user | ❌ |
| POST | /api/login | Login user/admin | ❌ |
| POST | /api/logout | Logout | ✅ |
| GET | /api/me | Data user login | ✅ |

### Mobil

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | /api/mobils | List mobil | ✅ |
| GET | /api/mobils/{id} | Detail mobil | ✅ |

### Booking

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | /api/bookings | List booking | ✅ |
| POST | /api/bookings | Buat booking baru | ✅ |
| GET | /api/bookings/{id} | Detail booking | ✅ |

### Pembayaran

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | /api/pembayarans | List pembayaran | ✅ |
| POST | /api/pembayarans | Catat pembayaran | ✅ |

## 👨‍💻 Tim Pengembang

| No | Nama | NIM |
|---|---|---|
| 1 | M. Teguh Gabita | 200102078 |
| 2 | Luthfi Rizalul Fikri | 230102066 |
| 3 | Fauzi Ardiansyah | 230102048 |
| 4 | Fahmy Muhammad Nurfadilah | 230102043 |
| 5 | Wijdan Fakhri Syauqi | 230102126 |

## 📝 Lisensi

Proyek ini dibuat untuk keperluan akademik.
