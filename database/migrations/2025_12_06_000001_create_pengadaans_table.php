<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengadaans', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan_spk', 50)->unique();
            $table->string('nama_pemohon', 150);
            $table->string('tipe_biaya', 50);
            $table->string('nama_project', 150)->nullable();
            $table->string('no_wbs', 50)->nullable();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->foreignId('bisnis_area_id')->constrained('bisnis_areas')->restrictOnDelete();
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->nullOnDelete();
            $table->date('tanggal_pesanan_spk');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('vendor_id');
            $table->index('bisnis_area_id');
            $table->index('cabang_id');
            $table->index('status_approval');
            $table->index('tanggal_pesanan_spk');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengadaans');
    }
};
