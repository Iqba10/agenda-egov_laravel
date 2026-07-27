<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpdGroup extends Model
{
    protected $fillable = [
        'name',
        'group_id',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function members(): HasMany
    {
        return $this->hasMany(OpdGroupMember::class, 'opd_group_id');
    }
}
