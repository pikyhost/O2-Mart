<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active', 'parent_id'];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    /**
     * Get the subcategories.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

}
