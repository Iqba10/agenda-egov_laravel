<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->index('status', 'agenda_status_idx');
            $table->index('waktu_mulai', 'agenda_waktu_mulai_idx');
            $table->index(['status', 'waktu_mulai'], 'agenda_status_waktu_mulai_idx');
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropIndex('agenda_status_idx');
            $table->dropIndex('agenda_waktu_mulai_idx');
            $table->dropIndex('agenda_status_waktu_mulai_idx');
        });
    }
};
