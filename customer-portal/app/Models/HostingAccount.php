<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostingAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain',
        'plan',
        'cpanel_username',
        'cpanel_url',
        'server',
        'status',
        'synergy_ref',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
