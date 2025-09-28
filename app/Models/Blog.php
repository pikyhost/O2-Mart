<?php

namespace App\Models;

use App\Traits\HasShareUrl;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Blog extends Model implements HasMedia
{
    use HasFactory, HasFilamentComments, InteractsWithMedia, Sluggable, HasShareUrl;

    protected $fillable = ['blog_category_id', 'title', 'content', 'author_id', 'is_active', 'published_at', 'meta_title', 'meta_description', 'alt_text'];

    protected $casts = [
        'published_at' => 'datetime'
    ];
    protected $appends = ['share_url'];

    public function getShareUrlAttribute(): string
    {
        $frontend = config('app.frontend_url');
        return "{$frontend}/Blog/{$this->slug}";
    }


    protected function getShareType(): string
    {
        return 'Blog';

    }

    /**
     * Configure the settings for generating slugs.
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title'],
        ];
    }

    /**
     * Get the route key name for Laravel routing.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tag');
    }

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_blog_image')->singleFile();

    }

    /**
     * Register media conversions for images.
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\Conversions\Conversion|\Spatie\MediaLibrary\MediaCollections\Models\Media|null $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(600)
            ->keepOriginalImageFormat()
            ->quality(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(1200)
            ->height(1200)
            ->keepOriginalImageFormat()
            ->quality(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(2000)
            ->height(2000)
            ->keepOriginalImageFormat()
            ->quality(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();
    }

    /**
     * Get the URL for the 'main_author_image' .
     */
    public function getMainBlogImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('main_blog_image', 'thumb') ?: null;
    }

    public function likers()
    {
        return $this->belongsToMany(User::class, 'blog_user_likes')->withTimestamps();
    }

    /**
     * Get comments related to the book.
     */
    public function filamentComments()
    {
        return $this->morphMany(\Parallax\FilamentComments\Models\FilamentComment::class, 'subject');
    }
}
