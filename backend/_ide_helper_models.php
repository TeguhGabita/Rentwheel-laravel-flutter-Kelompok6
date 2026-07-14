<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $mobil_id
 * @property int $user_id
 * @property string $tanggal_mulai
 * @property string $tanggal_selesai
 * @property numeric $total_harga
 * @property string $metode_pembayaran
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mobil $mobil
 * @property-read \App\Models\Pembayaran|null $pembayaran
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereMetodePembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereMobilId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUserId($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mobil> $mobils
 * @property-read int|null $mobils_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil whereNamaKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriMobil whereUpdatedAt($value)
 */
	class KategoriMobil extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $kategori_id
 * @property string $nama_mobil
 * @property string $merk
 * @property string $plat_nomor
 * @property numeric $harga_sewa_per_hari
 * @property string $status
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\KategoriMobil $kategori
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereHargaSewaPerHari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereMerk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereNamaMobil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil wherePlatNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobil whereUpdatedAt($value)
 */
	class Mobil extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property \Illuminate\Support\Carbon $tanggal_bayar
 * @property string $metode_bayar
 * @property int $jumlah_bayar
 * @property string $status_bayar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereJumlahBayar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereMetodeBayar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereStatusBayar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereTanggalBayar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereUpdatedAt($value)
 */
	class Pembayaran extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $no_ktp
 * @property string|null $no_hp
 * @property string|null $alamat
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User team($teams, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTeam($teams)
 */
	class User extends \Eloquent {}
}

