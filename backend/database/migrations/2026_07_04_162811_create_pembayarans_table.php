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
        $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
        $table->date('tanggal_bayar');
        $table->decimal('jumlah_bayar',12,2);
        $table->string('metode_bayar');
        $table->enum('status_bayar',['pending','lunas','gagal'])->default('pending');
        $table->string('bukti_pembayaran')->nullable();
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
