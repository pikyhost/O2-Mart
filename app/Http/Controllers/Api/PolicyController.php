<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PolicyController extends Controller
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
        $text = preg_replace('/!\[([^\]]*)\]\(([^\)]+)\)/', '<img src="$2" alt="$1" style="max-width: 100%; height: auto;" />', $text);
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        return $text;
    }
    public function privacy(): JsonResponse
    {
        $policy = Policy::first();
        $markdown = $policy?->privacy_policy;

        return response()->json([
            'title' => 'Privacy Policy',
            'markdown' => $markdown,
            'html' => $this->processMarkdown($markdown),
            'meta_title' => $policy?->meta_title_privacy_policy,
            'meta_description' => $policy?->meta_description_privacy_policy,
            'alt_text' => $policy?->alt_text_privacy_policy,
        ]);
    }

    public function refund(): JsonResponse
    {
        $policy = Policy::first();
        $markdown = $policy?->refund_policy;

        return response()->json([
            'title' => 'Warranty & Returns Policy',
            'markdown' => $markdown,
            'html' => $this->processMarkdown($markdown),
            'meta_title' => $policy?->meta_title_refund_policy,
            'meta_description' => $policy?->meta_description_refund_policy,
            'alt_text' => $policy?->alt_text_refund_policy,
        ]);
    }

    public function terms(): JsonResponse
    {
        $policy = Policy::first();
        $markdown = $policy?->terms_of_service;

        return response()->json([
            'title' => 'Terms of Use',
            'markdown' => $markdown,
            'html' => $this->processMarkdown($markdown),
            'meta_title' => $policy?->meta_title_terms_of_service,
            'meta_description' => $policy?->meta_description_terms_of_service,
            'alt_text' => $policy?->alt_text_terms_of_service,
        ]);
    }
}
