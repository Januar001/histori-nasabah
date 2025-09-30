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
        Schema::create('janji_bayar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_janji');
            $table->decimal('nominal_janji', 15, 2)->default(0);
            $table->enum('status', ['pending', 'sukses', 'gagal'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->string('created_by');
            $table->timestamps();
            
            $table->index('nasabah_id');
            $table->index('tanggal_janji');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('janji_bayar');
    }
};
