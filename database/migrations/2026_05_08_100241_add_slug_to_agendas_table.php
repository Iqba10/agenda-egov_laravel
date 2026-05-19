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
        Schema::table('agenda', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('perihal_kegiatan');
            $table->date('tanggal_surat')->nullable()->after('asal_surat');
            $table->text('keterangan')->nullable()->after('status');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['slug', 'tanggal_surat', 'keterangan', 'created_by']);
        });
    }
};
