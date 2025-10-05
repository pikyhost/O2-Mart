<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductSection;
use Illuminate\Http\Request;

class ProductSectionController extends Controller
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
        
        return $text;
    }
    public function show($type)
    {
        $section = ProductSection::where('type', $type)->first();

        if (!$section) {
            return response()->json([
                'status' => 'error',
                'message' => 'Section not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'background_image' => $section->background_image ? asset('storage/' . $section->background_image) : null,
                'section1_title'    => $section->section1_title,
                'section1_text1'    => $this->processMarkdown($section->section1_text1),
                'section1_text2'    => $this->processMarkdown($section->section1_text2),
                'section2_title'    => $section->section2_title,
                'section2_text'     => $this->processMarkdown($section->section2_text),
                'seo' => [
                    'meta_title'       => $section->meta_title,
                    'meta_description' => $section->meta_description,
                    'alt_text'         => $section->alt_text,
                ],
            ],
        ]);
    }
}
