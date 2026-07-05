<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_ktp')->nullable()->after('email');
            $table->string('no_hp')->nullable()->after('no_ktp');
            $table->text('alamat')->nullable()->after('no_hp');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropColumn('pelanggan_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_ktp', 'no_hp', 'alamat']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
