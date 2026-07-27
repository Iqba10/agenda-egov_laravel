<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpdGroupMember extends Model
{
    protected $fillable = [
        'opd_group_id',
        'nama',
        'phone_number',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(OpdGroup::class, 'opd_group_id');
    }
}
