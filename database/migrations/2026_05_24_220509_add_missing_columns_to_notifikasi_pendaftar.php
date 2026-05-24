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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('notifikasi_pendaftar', 'fcm_token_id')) {
                $table->unsignedBigInteger('fcm_token_id')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'channel_preference')) {
                $table->string('channel_preference', 20)->default('whatsapp')->after('fcm_token_id');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'whatsapp_sent')) {
                $table->boolean('whatsapp_sent')->default(false)->after('channel_preference');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'whatsapp_sent_at')) {
                $table->timestamp('whatsapp_sent_at')->nullable()->after('whatsapp_sent');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'fcm_sent')) {
                $table->boolean('fcm_sent')->default(false)->after('whatsapp_sent_at');
            }
            if (!Schema::hasColumn('notifikasi_pendaftar', 'fcm_sent_at')) {
                $table->timestamp('fcm_sent_at')->nullable()->after('fcm_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi_pendaftar', function (Blueprint $table) {
            // Only drop if they exist
            $columns = ['fcm_token_id', 'channel_preference', 'whatsapp_sent', 'whatsapp_sent_at', 'fcm_sent', 'fcm_sent_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('notifikasi_pendaftar', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
