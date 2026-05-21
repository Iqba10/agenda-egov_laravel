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
        Schema::table('dokumen_agenda', function (Blueprint $table) {
            $table->dropUnique('dokumen_agenda_content_hash_unique');
            $table->index('content_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_agenda', function (Blueprint $table) {
            $table->dropIndex(['content_hash']);
            $table->unique('content_hash');
        });
    }
};
