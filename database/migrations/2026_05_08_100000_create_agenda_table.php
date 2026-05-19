<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agenda')) {
            Schema::create('agenda', function (Blueprint $table) {
                $table->integer('id', true);
                $table->enum('jenis_agenda', ['eksternal', 'internal']);
                $table->text('perihal_kegiatan');
                $table->dateTime('waktu_mulai');
                $table->dateTime('waktu_selesai');
                $table->string('tempat', 255);
                $table->string('asal_surat', 255);
                $table->string('pakaian', 100);
                $table->text('disposisi')->nullable();
                $table->string('petugas_ditugaskan', 255);
                $table->enum('status', ['terjadwal', 'selesai', 'dibatalkan'])->default('terjadwal');
                $table->string('diinput_oleh', 100)->default('Admin Dinas')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};