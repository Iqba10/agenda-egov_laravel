<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('fcm_tokens', 'whatsapp_opt_in')) {
                $table->boolean('whatsapp_opt_in')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('fcm_tokens', 'whatsapp_name')) {
                $table->string('whatsapp_name', 100)->nullable()->after('whatsapp_opt_in');
            }
            if (!Schema::hasColumn('fcm_tokens', 'whatsapp_phone')) {
                $table->string('whatsapp_phone', 20)->nullable()->after('whatsapp_name');
            }
        });

        Schema::table('agenda', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda', 'reminder_6h_sent_at')) {
                $table->timestamp('reminder_6h_sent_at')->nullable()->after('reminder_sent_at');
            }
            if (!Schema::hasColumn('agenda', 'reminder_1h_sent_at')) {
                $table->timestamp('reminder_1h_sent_at')->nullable()->after('reminder_6h_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            foreach (['whatsapp_opt_in', 'whatsapp_name', 'whatsapp_phone'] as $col) {
                if (Schema::hasColumn('fcm_tokens', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('agenda', function (Blueprint $table) {
            foreach (['reminder_6h_sent_at', 'reminder_1h_sent_at'] as $col) {
                if (Schema::hasColumn('agenda', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
