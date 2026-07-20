<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_pendaftar', function (Blueprint $table) {
            if (!Schema::hasColumn('notifikasi_pendaftar', 'is_immediate')) {
                $table->boolean('is_immediate')->default(false)->after('reminder_minutes');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'immediate_sent')) {
                $table->boolean('immediate_sent')->default(false)->after('is_immediate');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'immediate_sent_at')) {
                $table->timestamp('immediate_sent_at')->nullable()->after('immediate_sent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_pendaftar', function (Blueprint $table) {
            $table->dropColumn(['is_immediate', 'immediate_sent', 'immediate_sent_at']);
        });
    }
};