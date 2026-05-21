<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dokumen_agenda', function (Blueprint $table) {
            $table->string('mime_type', 100)->nullable()->after('content_hash');
            $table->unsignedInteger('file_size')->nullable()->after('mime_type');
        });

        // Add LONGBLOB column for storing file content
        DB::statement('ALTER TABLE dokumen_agenda ADD COLUMN content LONGBLOB NULL AFTER content_hash');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_agenda', function (Blueprint $table) {
            $table->dropColumn(['content', 'mime_type', 'file_size']);
        });
    }
};
