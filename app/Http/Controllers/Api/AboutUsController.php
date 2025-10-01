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
    
    // Split content into lines to preserve exact line breaks
    $lines = explode("\n", $content);
    $html = '';
    $inList = false;
    $listType = '';
    
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        
        // Empty line
        if (empty($trimmedLine)) {
            if ($inList) {
                $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                $inList = false;
            }
            $html .= "<br>\n";
            continue;
        }
        
        // Headers
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
        
        // Unordered list items
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
        
        // Ordered list items
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
        
        // Regular paragraph
        if ($inList) {
            $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
            $inList = false;
        }
        
        $text = $this->processInlineMarkdown($trimmedLine);
        $html .= "<p>{$text}</p>\n";
    }
    
    // Close any remaining lists
    if ($inList) {
        $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
    }
    
    return trim($html);
}

private function processInlineMarkdown($text)
{
    // Links [text](url)
    $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
    
    // Bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Italic
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    
    // Code inline
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    
    // Already preserve any existing HTML spans (colored text)
    // These should pass through unchanged
    
    return $text;
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
