<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupEmail extends Model
{
    protected $fillable = ['email', 'popup_id'];
    
    public function popup()
    {
        return $this->belongsTo(\App\Models\Popup::class);
    }
}
