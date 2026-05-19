<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaReminder extends Model
{
    protected $fillable = [
        'nama',
        'phone_number',
        'channel',
        'is_sent',
        'sent_at',
        'agenda_id',
    ];

    protected function casts(): array
    {
        return [
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('is_sent', false);
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }
}
