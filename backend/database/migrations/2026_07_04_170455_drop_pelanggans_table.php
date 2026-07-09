<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pelanggans');
    }

    public function down(): void
    {
        Schema::create('pelanggans', function ($table) {
            $table->id();
            $table->string('nama');
            $table->string('no_ktp');
            $table->string('no_hp');
            $table->text('alamat');
            $table->timestamps();
        });
    }
};
