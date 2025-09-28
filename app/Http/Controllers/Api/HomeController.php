<?php

namespace App\Http\Controllers\Api;

use App\Models\HomeSection;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HomeController extends Controller
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
    // Bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Italic
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    
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
    
    public function index(): JsonResponse
    {
        $section = HomeSection::first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'tagline' => $section->tagline,
                'section_1' => [
                    'title' => $section->section_1_title,
                    'text'  => $this->processMarkdown($section->section_1_text),
                    'image' => $this->getImageUrl($section->section_1_image),
                     'cta'   => [
                        'text' => $section->section_1_cta_text,
                        'link' => $section->section_1_cta_link,
                    ],
                ],
                'section_2' => [
                    'title' => $section->section_2_title,
                    'text'  => $this->processMarkdown($section->section_2_text),
                    'image' => $this->getImageUrl($section->section_2_image),
                ],
                'section_3' => [
                    'title' => $section->section_3_title,
                    'text'  => $this->processMarkdown($section->section_3_text),
                    'image' => $this->getImageUrl($section->section_3_image),
                ],
                'section_4' => [
                    'boxes' => collect($section->categories_boxes)->map(function ($item) {
                        return [
                            'image' => $this->getImageUrl($item['image']),
                            'link' => $item['link'],
                        ];
                    }),
                ],
                'banners' => [
                    'banner_1' => [
                        'image' => $this->getImageUrl($section->banner_1_image),
                        'link' => $section->banner_1_link,
                    ],
                    'banner_2' => [
                        'image' => $this->getImageUrl($section->banner_2_image),
                        'link' => $section->banner_2_link,
                    ],
                ],
                'blog_section' => [
                    'title' => $section->blog_section_title,
                    'text'  => $this->processMarkdown($section->blog_section_text),
                ],
                'seo' => [
                    'meta_title'       => $section->meta_title,
                    'meta_description' => $section->meta_description,
                    'alt_text'         => $section->alt_text,
                ],

            ]
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

//finally solved the editor