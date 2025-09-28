<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $guarded = [];

    public function mobileVanService()
    {
        return $this->belongsTo(MobileVanService::class);
    }

    public function installerShop()
    {
        return $this->belongsTo(InstallerShop::class);
    }

}
