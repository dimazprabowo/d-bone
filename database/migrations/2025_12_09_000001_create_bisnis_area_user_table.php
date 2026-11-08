<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot user ↔ bisnis_area: user dengan role scoped (admin bisnis area,
     * senior manager) dibatasi hanya melihat pengadaan BA yang di-assign.
     * Dibaca oleh global scope di Pengadaan::booted() — user TANPA permission
     * `access_all_bisnis_area` hanya melihat BA pada pivot ini.
     */
    public function up(): void
    {
        Schema::create('bisnis_area_user', function (Blueprint $table) {
            $table->foreignId('bisnis_area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['bisnis_area_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bisnis_area_user');
    }
};
