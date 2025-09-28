<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name'];

    public function installerShops()
    {
        return $this->belongsToMany(InstallerShop::class);
    }

    public function mobileVans()
    {
        return $this->belongsToMany(MobileVanService::class);
    }
}

