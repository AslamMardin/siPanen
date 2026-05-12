<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_panens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kecamatan_id')->constrained('kecamatans');
            $table->foreignId('desa_id')->constrained('desas');
            $table->enum('musim_tanam', ['Musim Hujan', 'Musim Kemarau']);
            $table->year('tahun');
            $table->decimal('luas_tanam', 10, 2)->comment('hektar');
            $table->decimal('luas_panen', 10, 2)->comment('hektar');
            $table->decimal('produksi', 10, 2)->comment('ton');
            $table->decimal('produktivitas', 10, 4)->storedAs('produksi / luas_panen')->comment('ton/ha - auto calculated');
            $table->string('varietas_padi');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'menunggu_validasi', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_panens');
    }
};
