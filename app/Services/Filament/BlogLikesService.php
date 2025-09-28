<?php

namespace App\Services\Filament;

use App\Models\Blog;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class BlogLikesService
{
    protected array $likedBlogs = [];

    public function __construct()
    {
        if (empty($this->likedBlogs)) {
            $this->loadLikedBlogs();
        }
    }

    protected function loadLikedBlogs(): void
    {
        $this->likedBlogs = Filament::auth()->user()
            ? Filament::auth()->user()->likedBlogs()->pluck('blogs.id')->toArray()
            : [];
    }

    public function isBlogLiked(int $blogId): bool
    {
        return in_array($blogId, $this->likedBlogs);
    }

    public function toggleLike(Blog $blog): void
    {
        $user = Filament::auth()->user();

        if ($user && $this->isBlogLiked($blog->id)) {
            $user->likedBlogs()->detach($blog->id);
            $this->removeFromLikedCache($blog->id);
            $this->sendNotification('Removed from favorites', "{$blog->title} has been removed from your liked list.", 'danger');
        } elseif ($user) {
            $user->likedBlogs()->attach($blog->id);
            $this->addToLikedCache($blog->id);
            $this->sendNotification('Added to favorites', "{$blog->title} has been added to your liked list.", 'success');
        }
    }
    protected function addToLikedCache(int $blogId): void
    {
        $this->likedBlogs[] = $blogId;
    }

    protected function removeFromLikedCache(int $blogId): void
    {
        $this->likedBlogs = array_diff($this->likedBlogs, [$blogId]);
    }

    protected function sendNotification(string $title, string $message, string $color): void
    {
        Notification::make()
            ->body($message)
            ->{$color}()
            ->title($title)
            ->send();
    }

    public static function getInstance(): self
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self;
        }

        return $instance;
    }
}
