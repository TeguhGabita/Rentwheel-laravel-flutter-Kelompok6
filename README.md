# RentWheel

Aplikasi web untuk mengelola proses penyewaan mobil — mulai dari data armada, data pelanggan, hingga transaksi penyewaan (booking) dan dashboard laporan.

## Fitur Utama
- Autentikasi (Login, Register, Logout) — Laravel Breeze
- Manajemen Role & Permission (Admin, User) — Spatie Permission
- CRUD Mobil & Kategori Mobil
- CRUD Booking / Transaksi Penyewaan
- CRUD Pembayaran
- Dashboard statistik & grafik tren penyewaan
- REST API (JSON) untuk resource Mobil, Booking, Pembayaran
- Search & pagination pada setiap tabel data
- Aplikasi mobile (Flutter) — konsumsi REST API untuk login & data mobil

## Tech Stack
- Laravel 13
- PHP 8.3
- MySQL 8
- Blade + Laravel Breeze
- Spatie Laravel Permission
- Laravel Sanctum (REST API)
- Tailwind CSS
- Flutter (mobile client)

---

## Progress Terkini

Update berikut sudah ditambahkan di luar setup awal:

- **Restrukturisasi ERD** — tabel `pelanggans` dihapus, data pelanggan dikonsolidasikan ke tabel `users` dengan pembeda role Admin/User (Spatie Permission), agar tidak ada duplikasi konsep "penyewa" antara `users` dan `pelanggans`
- **Tampilan Login** — didesain ulang dengan tema RentWheel (aksen amber, layout split panel kiri-kanan, tanpa ilustrasi bergerak agar lebih clean)
- **Pemisahan Dashboard Admin & Beranda User** — setelah login, admin diarahkan ke `/dashboard`, sedangkan user diarahkan ke `/beranda`, masing-masing dengan tampilan berbeda
- **Proteksi akses berbasis role** — middleware `role:admin` dan `role:user` diterapkan di route `/dashboard` dan `/beranda`, sehingga user tidak bisa mengakses halaman admin dan sebaliknya (403 Forbidden jika dilanggar)
- **Navigasi dinamis sesuai role** — menu navbar otomatis menyesuaikan: admin melihat menu Dashboard, user melihat menu Beranda, Cari Mobil, Booking Saya, dan Pembayaran
- **Halaman Beranda User** — ringkasan booking aktif, riwayat booking, status pembayaran, serta menu cepat ke Cari Mobil, Booking Saya, dan Pembayaran
- **CRUD Mobil (web)** — halaman katalog mobil dengan pencarian & pagination, siap dipakai user untuk memilih unit sewa
- **CRUD Booking (web)** — user dapat melihat riwayat booking miliknya; admin dapat melihat seluruh booking
- **CRUD Pembayaran (web)** — pencatatan pembayaran per booking, otomatis memperbarui status booking menjadi "berjalan" setelah pembayaran tercatat
- **BookingSeeder** — seeder data dummy booking sudah dibuat dan terverifikasi berjalan tanpa error, melengkapi seluruh seeder (Role, User, Kategori, Mobil, Booking)
- **REST API dasar (Sanctum)** — endpoint `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/me`, serta resource Mobil, Booking, dan Pembayaran sudah tersedia untuk keperluan aplikasi Flutter
- **Aplikasi Flutter (tahap awal)** — halaman Login yang terhubung ke REST API Laravel, menyimpan token via `shared_preferences`, dan redirect ke halaman utama setelah berhasil login

### Masih dalam pengembangan
- Dashboard Admin (CRUD Kategori Mobil dari sisi admin, statistik & grafik) — saat ini masih kosong menunggu integrasi
- Form tambah/edit booking dan pembayaran dari sisi web (saat ini baru halaman daftar/riwayat)
- Halaman-halaman lanjutan di aplikasi Flutter (daftar mobil, booking, riwayat)

---

## Cara Setup dari Awal (Step by Step)

Bagian ini mendokumentasikan urutan setup project persis dari awal, supaya kalau ada yang perlu install ulang dari nol (laptop baru, project corrupt, dll) bisa diikuti tanpa nanya-nanya lagi.

### 1. Buat Project Laravel

```bash
composer create-project laravel/laravel rentwheel
cd rentwheel
```

### 2. Install Laravel Breeze (Autentikasi)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

Ini otomatis menyediakan halaman Login, Register, Logout, Forgot Password, beserta proteksi CSRF bawaan Laravel.

### 3. Install Spatie Permission (Role & Permission)

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 4. Install Laravel Sanctum (untuk REST API)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Karena Laravel 13 fresh install tidak otomatis membuat `routes/api.php`, file ini perlu dibuat manual:

```php
// routes/api.php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

### 5. Setting `.env`

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

Buat database `rentwheel` di MySQL/Laragon terlebih dahulu sebelum migrate.

### 6. Daftarkan Middleware Role di `bootstrap/app.php`

Laravel 13 mendaftarkan middleware lewat `bootstrap/app.php`, bukan `Kernel.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

### 7. Tambahkan Trait `HasRoles` ke Model User

Di `app/Models/User.php`, pastikan:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}
```

### 8. Buat Migration Tabel Sesuai ERD

```bash
php artisan make:migration create_kategoris_table
php artisan make:migration create_mobils_table
php artisan make:migration create_bookings_table
php artisan make:migration create_pembayarans_table
```

Struktur tabel: `kategoris` → `mobils` (relasi ke kategori) → `bookings` (relasi ke mobil dan user) → `pembayarans` (relasi ke booking).

> Catatan: tabel `pelanggans` yang sempat dibuat di awal pengembangan sudah dihapus (lihat bagian Progress Terkini). Data penyewa sekarang seluruhnya berada di tabel `users`.

### 9. Buat Model

```bash
php artisan make:model Kategori
php artisan make:model Mobil
php artisan make:model Booking
php artisan make:model Pembayaran
```

### 10. Buat Seeder (Role, User, Data Dummy)

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder KategoriSeeder
php artisan make:seeder MobilSeeder
php artisan make:seeder BookingSeeder
```

Daftarkan semua di `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
        KategoriSeeder::class,
        MobilSeeder::class,
        BookingSeeder::class,
    ]);
}
```

⚠️ Pastikan baris bawaan `User::factory()->create([...])` (yang bikin "Test User") sudah dihapus dari `DatabaseSeeder.php`.

### 11. Jalankan Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

### 12. Jalankan Project

```bash
php artisan serve
```

Akses di `http://localhost:8000`, coba login dengan akun default di bawah.

---

## Instalasi Cepat (Untuk Anggota Tim yang Clone Repo)

Kalau project sudah ada di GitHub (langkah di atas sudah dilakukan sekali oleh Ketua Tim), anggota lain cukup:

```bash
git clone https://github.com/TeguhGabita/Rentwheel-laravel-flutter-Kelompok6.git
cd Rentwheel-laravel-flutter-Kelompok6/backend

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Sesuaikan `.env` dengan database masing-masing, lalu:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

### Untuk bagian Flutter (`frontend/`)

```bash
cd ../frontend
flutter pub get
flutter run
```

Sesuaikan `baseUrl` di `lib/services/api_service.dart` dengan alamat backend:
- Emulator Android: `http://10.0.2.2:8000/api`
- HP fisik / web: `http://<IP-komputer>:8000/api`

## Akun Default

| Role  | Email                   | Password |
|-------|------------------------ |----------|
| Admin | admin@rentwheel.test    | password |
| User  | user@rentwheel.test     | password |

## Screenshot




## Tim Pengembang
- M. Teguh Gabita (200102078) 
- Luthfi Rizalul Fikri (230102066) 
- Fauzi Ardiansyah (230102048) 
- Fahmy Muhammad Nurfadilah (230102043) 
- Wijdan Fakhri Syauqi -(230102126)
