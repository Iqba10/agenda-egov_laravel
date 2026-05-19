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
        Schema::create('dokumen_agenda', function (Blueprint $table) {
            $table->id();
            $table->integer('agenda_id')->index();
            $table->string('nama_file');
            $table->string('original_name');
            $table->timestamps();

            $table->foreign('agenda_id')->references('id')->on('agenda')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_agenda');
    }
};
