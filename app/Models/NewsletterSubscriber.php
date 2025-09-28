<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NewsletterSubscriber extends Model
{
    use HasFactory, Notifiable  ; 


    protected $fillable = ['email', 'ip_address', 'verified_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    public function routeNotificationFor(): string
    {
        return $this->email;
    }
}
