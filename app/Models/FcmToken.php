<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'token',
        'device_name',
        'subscribed_agendas',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_agendas' => 'array',
            'is_active'          => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function subscribeToAgenda(int $agendaId): void
    {
        $agendas = $this->subscribed_agendas ?? [];

        if (!in_array($agendaId, $agendas)) {
            $agendas[] = $agendaId;
            $this->update(['subscribed_agendas' => $agendas]);
        }
    }

    public function unsubscribeFromAgenda(int $agendaId): void
    {
        $agendas = $this->subscribed_agendas ?? [];
        $agendas = array_values(array_diff($agendas, [$agendaId]));
        $this->update(['subscribed_agendas' => $agendas]);
    }

    public function isSubscribedTo(int $agendaId): bool
    {
        return in_array($agendaId, $this->subscribed_agendas ?? []);
    }

    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)->first();
    }
}
