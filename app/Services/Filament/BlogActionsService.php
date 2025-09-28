<?php

namespace App\Services\Filament;

use App\Models\Blog;

class BlogActionsService
{
    public static function getLikeActionLabel(Blog $blog): string
    {
        return BlogLikesService::getInstance()->isBlogLiked($blog->id)
            ? 'Remove Like'
            : 'Like';
    }

    public static function getLikeActionIcon(Blog $blog): string
    {
        return BlogLikesService::getInstance()->isBlogLiked($blog->id)
            ? 'heroicon-m-no-symbol'
            : 'heroicon-o-hand-thumb-up';
    }

    public static function getLikeActionColor(Blog $blog): string
    {
        return BlogLikesService::getInstance()->isBlogLiked($blog->id)
            ? 'gray'
            : 'info';
    }

    public static function toggleLikeBlog(Blog $blog): void
    {
        BlogLikesService::getInstance()->toggleLike($blog);
    }
}
