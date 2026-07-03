<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('mobils', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kategori_id')->constrained()->onDelete('cascade');
        $table->string('nama_mobil');
        $table->string('merk');
        $table->string('plat_nomor')->unique();
        $table->decimal('harga_sewa_per_hari', 10, 2);
        $table->enum('status', ['tersedia','disewa','servis'])->default('tersedia');
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobils');
    }
};
