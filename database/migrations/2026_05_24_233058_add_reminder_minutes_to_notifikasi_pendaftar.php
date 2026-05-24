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
        Schema::table('notifikasi_pendaftar', function (Blueprint $table) {
            // Waktu pengingat dalam menit sebelum agenda (default 60 = 1 jam)
            $table->unsignedInteger('reminder_minutes')->default(60)->after('channel_preference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi_pendaftar', function (Blueprint $table) {
            $table->dropColumn('reminder_minutes');
        });
    }
};
