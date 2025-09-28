<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogUserLike extends Model
{
    use HasFactory;

    protected $table = 'blog_user_likes';

    protected $fillable = ['blog_id', 'user_id'];

    /**
     * Relationship to Blog.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Relationship to User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
