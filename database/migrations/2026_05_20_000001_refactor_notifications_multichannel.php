<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create/recreate agenda_reminders untuk multichannel (WA + FCM)
        Schema::dropIfExists('agenda_reminders');
        Schema::create('agenda_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('phone_number', 20);
            $table->enum('channel', ['whatsapp', 'fcm'])->default('whatsapp');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->integer('agenda_id');
            $table->timestamps();

            $table->unique(['phone_number', 'agenda_id']);
            $table->index('phone_number');
            $table->index('agenda_id');
            $table->foreign('agenda_id')->references('id')->on('agenda')->cascadeOnDelete();
        });

        // 2. Create fcm_tokens table
        Schema::dropIfExists('fcm_tokens');
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 500)->unique();
            $table->string('device_name', 255)->nullable();
            $table->json('subscribed_agendas')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // 3. Create notifikasi_pendaftar for public subscribers
        Schema::dropIfExists('notifikasi_pendaftar');
        Schema::create('notifikasi_pendaftar', function (Blueprint $table) {
            $table->id();
            $table->integer('agenda_id');
            $table->string('nama', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->unsignedBigInteger('fcm_token_id')->nullable();
            $table->enum('channel_preference', ['whatsapp', 'fcm', 'both'])->default('whatsapp');
            $table->boolean('whatsapp_sent')->default(false);
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->boolean('fcm_sent')->default(false);
            $table->timestamp('fcm_sent_at')->nullable();
            $table->timestamps();

            $table->index('agenda_id');
            $table->index('phone_number');
            $table->foreign('agenda_id')->references('id')->on('agenda')->cascadeOnDelete();
            $table->foreign('fcm_token_id')->references('id')->on('fcm_tokens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_pendaftar');
        Schema::dropIfExists('fcm_tokens');
        Schema::dropIfExists('agenda_reminders');
    }
};
