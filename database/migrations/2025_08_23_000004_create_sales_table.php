<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_transfer')->nullable();
            $table->date('tanggal_lead_masuk')->nullable();
            $table->integer('periode_lead_bulan')->nullable();
            $table->string('nama_lengkap_mitra')->nullable();
            $table->string('no_wa')->nullable();
            $table->integer('umur')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('lead_awal')->nullable();
            $table->string('sumber')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('nominal_masuk')->nullable();
            $table->bigInteger('harga_paket')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
