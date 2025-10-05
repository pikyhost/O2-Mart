<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private function processMarkdown($content)
    {
        if (!$content) return null;
        
        $lines = explode("\n", $content);
        $html = '';
        $inList = false;
        $listType = '';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            if (empty($trimmedLine)) {
                if ($inList) {
                    $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $inList = false;
                }
                $html .= "<br>\n";
                continue;
            }
            
            if (preg_match('/^(#{1,6})\s+(.+)/', $trimmedLine, $matches)) {
                if ($inList) {
                    $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $inList = false;
                }
                $level = strlen($matches[1]);
                $text = $this->processInlineMarkdown($matches[2]);
                $html .= "<h{$level}>{$text}</h{$level}>\n";
                continue;
            }
            
            if (preg_match('/^\*\s+(.+)/', $trimmedLine, $matches)) {
                if (!$inList || $listType !== 'ul') {
                    if ($inList) $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $html .= "<ul>\n";
                    $inList = true;
                    $listType = 'ul';
                }
                $text = $this->processInlineMarkdown($matches[1]);
                $html .= "<li>{$text}</li>\n";
                continue;
            }
            
            if (preg_match('/^\d+\.\s+(.+)/', $trimmedLine, $matches)) {
                if (!$inList || $listType !== 'ol') {
                    if ($inList) $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $html .= "<ol>\n";
                    $inList = true;
                    $listType = 'ol';
                }
                $text = $this->processInlineMarkdown($matches[1]);
                $html .= "<li>{$text}</li>\n";
                continue;
            }
            
            if ($inList) {
                $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                $inList = false;
            }
            
            $text = $this->processInlineMarkdown($trimmedLine);
            $html .= "<p>{$text}</p>\n";
        }
        
        if ($inList) {
            $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
        }
        
        return trim($html);
    }

    private function processInlineMarkdown($text)
    {
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        return $text;
    }
    
    public function categories(Request $request)
    {
        $categories = BlogCategory::withCount(['blogs' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name, // Direct access since only English
                    'slug' => $category->slug,
                    'blogs_count' => $category->blogs_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Blog categories retrieved successfully'
        ]);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $blogs = Blog::with(['category', 'author', 'tags'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) {
            $excerptMarkdown = $blog->content;
            $excerptHtml = $this->processMarkdown($excerptMarkdown);

            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt_markdown' => $excerptMarkdown,
                'excerpt_html' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                ] : null,
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
                ],
                'image_url' => $blog->getMainBlogImageUrl(),
                'likes_count' => $blog->likers()->count(),
                'meta_title'       => $blog->meta_title,
                'meta_description' => $blog->meta_description,
                'alt_text'         => $blog->alt_text,
                'share_url'        => $blog->share_url,

                'tags' => $blog->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name, // Direct access since single column
                    ];
                }),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs retrieved successfully'
        ]);
    }

    public function show(Request $request, $slug)
    {
        $blog = Blog::with(['category', 'author', 'tags', 'likers'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Convert Markdown to HTML for main blog
        $contentMarkdown = $blog->content;
        $contentHtml = $this->processMarkdown($contentMarkdown);

        // Fetch and convert related blogs
        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($relatedBlog) {
                $excerptMarkdown = $relatedBlog->content;
                $excerptHtml = $this->processMarkdown($excerptMarkdown);

                return [
                    'id' => $relatedBlog->id,
                    'title' => $relatedBlog->title,
                    'slug' => $relatedBlog->slug,
                    'excerpt_markdown' => $excerptMarkdown,
                    'excerpt_html' => $excerptHtml,
                    'published_at' => $relatedBlog->published_at->format('Y-m-d'),
                    'image_url' => $relatedBlog->getMainBlogImageUrl(),
                ];
            });

        // Main blog response
        $response = [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'content_markdown' => $contentMarkdown,
            'content_html' => $contentHtml,
            'published_at' => $blog->published_at->format('Y-m-d'),
            'category' => $blog->category ? [
                'id' => $blog->category->id,
                'name' => $blog->category->name,
                'slug' => $blog->category->slug,
            ] : null,
            'author' => [
                'id' => $blog->author->id,
                'name' => $blog->author->name,
                'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                'avatar' => asset('storage/' . $blog->author->avatar_url)
            ],
            'image_url' => $blog->getMainBlogImageUrl(),
            'likes_count' => $blog->likers->count(),
            'is_liked' => Auth::guard('sanctum')->user() ? $blog->likers->contains(Auth::guard('sanctum')->user()->id) : false,
            'tags' => $blog->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
            'related_blogs' => $relatedBlogs,
            'meta_title'       => $blog->meta_title,
            'meta_description' => $blog->meta_description,
            'alt_text'         => $blog->alt_text,
            'share_url'        => $blog->share_url,

        ];

        return response()->json([
            'success' => true,
            'data' => $response,
            'message' => 'Blog retrieved successfully'
        ]);
    }

    public function byCategory(Request $request, $categoryId)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Get the parent category
        $category = BlogCategory::where('id', $categoryId)
            ->where('is_active', true)
            ->firstOrFail();

        // Get all subcategory IDs (1-level deep, you can make it recursive if needed)
        $subcategoryIds = BlogCategory::where('parent_id', $category->id)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        // Include the parent category ID as well
        $categoryIds = array_merge([$category->id], $subcategoryIds);

        // Get blogs for all matching categories
        $blogs = Blog::with(['author', 'tags'])
            ->whereIn('blog_category_id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transform blogs
        $transformedBlogs = $blogs->getCollection()->map(function ($blog) {
            $excerptMarkdown = $blog->content;
            $excerptHtml = $this->processMarkdown($excerptMarkdown);

            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt_markdown' => $excerptMarkdown,
                'excerpt_html' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
                ],
                'image_url' => $blog->getMainBlogImageUrl(),
                'likes_count' => $blog->likers()->count(),
                'share_url'        => $blog->share_url,
                'tags' => $blog->tags->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
            ];
        });

        // Include pagination and category data
        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;
        $paginatedResponse['category'] = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ];

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs by parent category and subcategories retrieved successfully',
        ]);
    }

    public function byTag(Request $request, $tagId)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $tag = Tag::findOrFail($tagId);

        $blogs = Blog::with(['author', 'category'])
            ->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId); // ← Fixed here
            })
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) {
            $excerptMarkdown = $blog->content;
            $excerptHtml = $this->processMarkdown($excerptMarkdown);

            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt_markdown' => $excerptMarkdown,
                'excerpt_html' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
                ],
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                ] : null,
                'image_url' => $blog->getMainBlogImageUrl(),
                'share_url'        => $blog->share_url,
                'likes_count' => $blog->likers()->count(),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;
        $paginatedResponse['tag'] = [
            'id' => $tag->id,
            'name' => $tag->name,
        ];

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs by tag retrieved successfully'
        ]);
    }


    public function recent(Request $request)
    {
        $limit = $request->input('limit', 5);

        $blogs = Blog::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($blog) {
                $excerptMarkdown = $blog->content;
                $excerptHtml = $this->processMarkdown($excerptMarkdown);

                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'excerpt_markdown' => $excerptMarkdown,
                    'excerpt_html' => $excerptHtml,
                    'published_at' => $blog->published_at->format('Y-m-d'),
                    'image_url' => $blog->getMainBlogImageUrl(),
                    'share_url'        => $blog->share_url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'message' => 'Recent blogs retrieved successfully'
        ]);
    }

    public function toggleLike(Request $request, $blogId)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $blog = Blog::findOrFail($blogId);

        $liked = $blog->likers()->toggle($user->id);

        $likesCount = $blog->likers()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'likes_count' => $likesCount,
                'is_liked' => $blog->likers()->where('user_id', $user->id)->exists(),
            ],
            'message' => $liked['attached'] ? 'Blog liked successfully' : 'Blog unliked successfully',
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $blogs = Blog::with(['author', 'category'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) {
            $excerptMarkdown = $blog->content;
            $excerptHtml = $this->processMarkdown($excerptMarkdown);

            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt_markdown' => $excerptMarkdown,
                'excerpt_html' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
                ],
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                ] : null,
                'image_url' => $blog->getMainBlogImageUrl(),
                'share_url'        => $blog->share_url,
                'likes_count' => $blog->likers()->count(),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;
        $paginatedResponse['search_query'] = $query;

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Search results retrieved successfully'
        ]);
    }

    public function getTags()
    {
        $tags = Tag::query()
            ->select('id', 'name')
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tags,
            'message' => 'Tags retrieved successfully'
        ]);
    }
}
