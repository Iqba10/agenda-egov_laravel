<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiPendaftar extends Model
{
    protected $table = 'notifikasi_pendaftar';

    protected $fillable = [
        'agenda_id',
        'nama',
        'phone_number',
        'fcm_token_id',
        'channel_preference',
        'status',
        'whatsapp_sent',
        'whatsapp_sent_at',
        'fcm_sent',
        'fcm_sent_at',
        'sudah_dikirim',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_sent'    => 'boolean',
            'whatsapp_sent_at' => 'datetime',
            'fcm_sent'         => 'boolean',
            'fcm_sent_at'      => 'datetime',
        ];
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function fcmToken(): BelongsTo
    {
        return $this->belongsTo(FcmToken::class);
    }

    public function scopePending($query)
    {
        return $query->where(function ($q) {
            $q->where('whatsapp_sent', false)
              ->orWhere('fcm_sent', false);
        });
    }

    public function scopeNeedWhatsapp($query)
    {
        return $query->where('whatsapp_sent', false)
            ->whereIn('channel_preference', ['whatsapp', 'both'])
            ->whereNotNull('phone_number');
    }

    public function scopeNeedFcm($query)
    {
        return $query->where('fcm_sent', false)
            ->whereIn('channel_preference', ['fcm', 'both'])
            ->whereNotNull('fcm_token_id');
    }

    public function markWhatsappSent(): void
    {
        $this->update([
            'whatsapp_sent'    => true,
            'whatsapp_sent_at' => now(),
        ]);
    }

    public function markFcmSent(): void
    {
        $this->update([
            'fcm_sent'    => true,
            'fcm_sent_at' => now(),
        ]);
    }

    public function isComplete(): bool
    {
        return match ($this->channel_preference) {
            'whatsapp' => $this->whatsapp_sent,
            'fcm'      => $this->fcm_sent,
            'both'     => $this->whatsapp_sent && $this->fcm_sent,
            default    => false,
        };
    }
}
