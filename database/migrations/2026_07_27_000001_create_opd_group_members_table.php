<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opd_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_group_id')->constrained('opd_groups')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('phone_number', 20);
            $table->timestamps();

            $table->unique(['opd_group_id', 'phone_number']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd_group_members');
    }
};
