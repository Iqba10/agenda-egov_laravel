<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model
{
    protected $table = 'agenda';

    protected $fillable = [
        'jenis_agenda',
        'perihal_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'asal_surat',
        'tanggal_surat',
        'pakaian',
        'disposisi',
        'petugas_ditugaskan',
        'status',
        'keterangan',
        'diinput_oleh',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Agenda $agenda) {
            if (empty($agenda->slug) || $agenda->isDirty('perihal_kegiatan') || $agenda->isDirty('waktu_mulai')) {
                $dateStr = $agenda->waktu_mulai ? \Carbon\Carbon::parse($agenda->waktu_mulai)->translatedFormat('d F Y') : '';
                $baseSlug = \Illuminate\Support\Str::slug($agenda->perihal_kegiatan . '-' . $dateStr);
                
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $agenda->id)->exists()) {
                    $slug = $baseSlug . '-' . $count++;
                }
                
                $agenda->slug = $slug;
            }

            // Keep persisted status in sync with agenda timestamps.
            // "dibatalkan" stays manual; other states follow schedule automatically.
            // Note: "berlangsung" is a computed state, not stored in DB.
            if ($agenda->status !== 'dibatalkan') {
                $computed = $agenda->computedStatus();
                // Only persist valid DB enum values (berlangsung is computed-only)
                $agenda->status = $computed === 'berlangsung' ? 'terjadwal' : $computed;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AgendaDocument::class, 'agenda_id');
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!$status || $status === 'semua') {
            return $query;
        }

        $now = now()->toDateTimeString();

        // Filter by effective status based on timestamps using application timezone
        return $query->whereRaw("
            CASE
                WHEN status = 'dibatalkan' THEN 'dibatalkan'
                WHEN ? < waktu_mulai THEN 'terjadwal'
                WHEN ? BETWEEN waktu_mulai AND waktu_selesai THEN 'berlangsung'
                ELSE 'selesai'
            END = ?
        ", [$now, $now, $status]);
    }

    public function getEffectiveStatusAttribute(): string
    {
        return $this->computedStatus();
    }

    public function computedStatus(): string
    {
        if ($this->status === 'dibatalkan') {
            return 'dibatalkan';
        }

        if (! $this->waktu_mulai || ! $this->waktu_selesai) {
            return $this->status;
        }

        $now = now();

        if ($now->lt($this->waktu_mulai)) {
            return 'terjadwal';
        }

        if ($now->between($this->waktu_mulai, $this->waktu_selesai)) {
            return 'berlangsung';
        }

        return 'selesai';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        $base = 'inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold';
        return match ($this->effective_status) {
            'selesai'     => $base . ' bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
            'dibatalkan'  => $base . ' bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
            'berlangsung' => $base . ' bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200',
            default       => $base . ' bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-200',
        };
    }
}
