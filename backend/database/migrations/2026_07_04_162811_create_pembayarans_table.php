<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id')->constrained()->onDelete('cascade');
        $table->date('tanggal_bayar');
        $table->string('metode_bayar');
        $table->integer('jumlah_bayar');
        $table->enum('status_bayar', ['pending', 'lunas', 'gagal'])->default('pending');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
