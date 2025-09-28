<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'privacy_policy',
        'refund_policy',
        'terms_of_service',
        'meta_title',
        'meta_description',
        'alt_text',
        'meta_title_privacy_policy',
        'meta_description_privacy_policy',
        'alt_text_privacy_policy',
        'meta_title_refund_policy',
        'meta_description_refund_policy',
        'alt_text_refund_policy',
        'meta_title_terms_of_service',
        'meta_description_terms_of_service',
        'alt_text_terms_of_service',
    ];

    public static function getPolicy(string $policyType): ?string
    {
        return static::first()?->{$policyType};
    }
}
