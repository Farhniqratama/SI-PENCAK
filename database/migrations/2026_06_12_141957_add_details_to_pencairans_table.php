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
        Schema::table('pencairans', function (Blueprint $table) {
            $table->decimal('nominal_pencairan', 20, 2)->nullable()->after('jumlah_mahasiswa');
            $table->string('jenis_bantuan')->nullable()->after('nominal_pencairan');
            $table->text('keterangan')->nullable()->after('jenis_bantuan');
            $table->string('surat_pengantar')->nullable()->after('berita_acara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pencairans', function (Blueprint $table) {
            $table->dropColumn(['nominal_pencairan', 'jenis_bantuan', 'keterangan', 'surat_pengantar']);
        });
    }
};
