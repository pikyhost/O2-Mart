<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AboutUsController extends Controller
{
    private function processMarkdown($content)
    {
        if (!$content) return null;
        
        // Handle multiple line breaks before markdown processing
        $content = preg_replace_callback('/\n{3,}/', function($matches) {
            return str_repeat('<br>', substr_count($matches[0], "\n"));
        }, $content);
        
        // Fix bullet list continuity - ensure each * starts a new list item
        $content = preg_replace('/\n(?=\* )/', "\n\n", $content);
        
        // Process markdown
        return Str::markdown($content);
    }
    
    private function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) return null;
        
        // Remove any existing domain from the path
        $cleanPath = preg_replace('#^https?://[^/]+/#', '', $imagePath);
        $cleanPath = ltrim($cleanPath, '/');
        
        // If it starts with 'storage/', use it as is, otherwise prepend 'storage/'
        if (!str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = 'storage/' . $cleanPath;
        }
        
        // Check if file exists, if not return null
        $fullPath = public_path($cleanPath);
        if (!file_exists($fullPath)) {
            \Log::warning("Missing image file: {$fullPath}");
            return null;
        }
        
        return url($cleanPath);
    }
    
    /**
     * Retrieve the About Us data.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $aboutUs = AboutUs::first();

        if (!$aboutUs) {
            return response()->json([
                'message' => 'About Us data not found',
            ], 404);
        }

        // Transform file paths to full URLs if they exist
        $data = $aboutUs->toArray();

        // Convert all file paths to full URLs using getImageUrl method
        $fileFields = [
            'about_us_video_path',
            'center_image_path',
            'latest_image_path',
            'slider_1_image',
            'slider_2_image',
            'intro_image'
        ];

        foreach ($fileFields as $field) {
            $data[$field] = $this->getImageUrl($aboutUs->$field);
        }
        $data['video_url'] = $aboutUs->getFirstMediaUrl('about_us_video') ?: null;
        $data['meta_title']       = $aboutUs->meta_title;
        $data['meta_description'] = $aboutUs->meta_description;
        $data['alt_text']         = $aboutUs->alt_text;
        
        // Process markdown fields
        $data['intro_text'] = $this->processMarkdown($aboutUs->intro_text);
        $data['center_text'] = $this->processMarkdown($aboutUs->center_text);
        $data['latest_text'] = $this->processMarkdown($aboutUs->latest_text);

        return response()->json([
            'data' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
