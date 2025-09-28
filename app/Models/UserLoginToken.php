<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'session_id',
        'is_login',
    ];

    public function user(): BelongsTo
    {
        return $this
            ->belongsTo(User::class,
            'user_id',
            'id'
        );
    }

    protected function casts(): array
    {
        return [
            'is_login' => 'boolean',
        ];
    }

}
