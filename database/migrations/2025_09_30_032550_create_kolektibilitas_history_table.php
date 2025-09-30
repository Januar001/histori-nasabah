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
        Schema::create('kolektibilitas_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained()->onDelete('cascade');
            $table->enum('kolektibilitas_sebelum', ['1', '2', '3', '4', '5']);
            $table->enum('kolektibilitas_sesudah', ['1', '2', '3', '4', '5']);
            $table->date('tanggal_perubahan');
            $table->string('petugas');
            $table->foreignId('petugas_id')->nullable()->constrained('petugas')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('nasabah_id');
            $table->index('tanggal_perubahan');
            $table->index('petugas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kolektibilitas_history');
    }
};
