<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'expires_at',
        'min_order_amount',
        'is_active',
        'usage_limit',
        'usage_limit_per_user'
    ];

    protected $casts = [
        'value' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    protected static $logName = 'coupon';
}
