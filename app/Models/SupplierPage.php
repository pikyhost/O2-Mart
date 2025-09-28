<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPage extends Model
{
    use HasFactory;

    protected $table = 'suppliers_page'; // Explicit table name

    protected $fillable = [
        'title_become_supplier',
        'desc_become_supplier',
        'why_auto_title',
        'why_auto_desc',
        'meta_title',
        'meta_description',
        'alt_text',
    ];
}
