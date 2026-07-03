# RentWheel

Aplikasi web untuk mengelola proses penyewaan mobil — mulai dari data armada, data pelanggan, hingga transaksi penyewaan (booking) dan dashboard laporan.

## Fitur Utama
- Autentikasi (Login, Register, Logout) — Laravel Breeze
- Manajemen Role & Permission (Admin, User) — Spatie Permission
- CRUD Mobil & Kategori Mobil
- CRUD Pelanggan
- CRUD Booking / Transaksi Penyewaan
- Dashboard statistik & grafik tren penyewaan
- REST API (JSON) untuk resource Mobil & Booking
- Search & pagination pada setiap tabel data

## Tech Stack
- Laravel 13
- PHP 8.3
- MySQL 8
- Blade + Laravel Breeze
- Spatie Laravel Permission
- Laravel Sanctum (REST API)
- Tailwind CSS

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
DB_PASSWORD=
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
php artisan make:migration create_pelanggans_table
php artisan make:migration create_bookings_table
```

Struktur tabel: `kategoris` → `mobils` (relasi ke kategori) → `pelanggans` → `bookings` (relasi ke mobil, pelanggan, dan user).

### 9. Buat Model

```bash
php artisan make:model Kategori
php artisan make:model Mobil
php artisan make:model Pelanggan
php artisan make:model Booking
```

### 10. Buat Seeder (Role, User, Data Dummy)

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder KategoriSeeder
php artisan make:seeder MobilSeeder
php artisan make:seeder PelangganSeeder
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
        PelangganSeeder::class,
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
git clone https://github.com/username/rentwheel.git
cd rentwheel/backend

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

## Akun Default

| Role  | Email                 | Password |
|-------|------------------------|----------|
| Admin | admin@rentwheel.test    | password |
| User  | user@rentwheel.test     | password |

## Screenshot


## Tim
- M.Teguh Gabita (200102078) — Ketua Tim / Backend Lead (Setup, Migration & Seeder, Autentikasi, Role & Permission, Git Workflow)
- Fahmy Muhammad Nurfadilah (230102043) — CRUD Mobil & Kategori
- Fauzi Ardiansyah (230102044— CRUD Pelanggan & Booking
- Luthfi Rizalul Fikri (230102066) — Dashboard, REST API, UI/UX & QA/Dokumentasi
