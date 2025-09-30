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
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('kantor')->nullable();
            $table->string('nocif');
            $table->string('rekening');
            $table->string('namadb');
            $table->date('tglpinjam')->nullable();
            $table->date('tgltempo')->nullable();
            $table->decimal('plafon', 15, 2)->default(0);
            $table->decimal('rate', 5, 2)->default(0);
            $table->decimal('nompokok', 15, 2)->default(0);
            $table->decimal('hrpokok', 15, 2)->default(0);
            $table->decimal('xtungpok', 15, 2)->default(0);
            $table->decimal('nombunga', 15, 2)->default(0);
            $table->decimal('hrbunga', 15, 2)->default(0);
            $table->decimal('xtungbu', 15, 2)->default(0);
            $table->decimal('bakidebet', 15, 2)->default(0);
            $table->enum('kualitas', ['1', '2', '3', '4', '5'])->default('1');
            $table->decimal('nilckpn', 15, 2)->default(0);
            $table->decimal('nilliquid', 15, 2)->default(0);
            $table->decimal('nilnliquid', 15, 2)->default(0);
            $table->decimal('min_ppap', 15, 2)->default(0);
            $table->decimal('ppapwd', 15, 2)->default(0);
            $table->date('tgl_macet')->nullable();
            $table->text('alamat')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('dati2')->nullable();
            $table->string('sifat')->nullable();
            $table->string('jenis')->nullable();
            $table->string('kategori_deb')->nullable();
            $table->string('sektor')->nullable();
            $table->string('jnsguna')->nullable();
            $table->string('goldeb')->nullable();
            $table->string('jnskre')->nullable();
            $table->string('nopk')->nullable();
            $table->text('catatan')->nullable();
            $table->string('ketproduk')->nullable();
            $table->string('kdao')->nullable();
            $table->string('namaao')->nullable();
            $table->string('jbpkb')->nullable();
            $table->string('jsertifikat')->nullable();
            $table->string('jlain2')->nullable();
            $table->string('ciflama')->nullable();
            $table->string('rekeninglama')->nullable();
            $table->string('kdkondisi')->nullable();
            $table->date('tglunas')->nullable();
            $table->decimal('bakidb', 15, 2)->default(0);
            $table->foreignId('petugas_id')->nullable()->constrained('petugas')->onDelete('set null');
            $table->date('tanggal_ditangani')->nullable();
            $table->text('catatan_penanganan')->nullable();
            $table->timestamps();
            
            $table->index('nocif');
            $table->index('rekening');
            $table->index('kualitas');
            $table->index('petugas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasabahs');
    }
};
