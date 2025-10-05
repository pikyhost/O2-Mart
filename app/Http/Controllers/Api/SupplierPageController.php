<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SupplierPageController extends Controller
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

    public function show(): JsonResponse
    {
        $supplierPage = SupplierPage::first();

        if (! $supplierPage) {
            return response()->json(['message' => 'Supplier page not found'], 404);
        }

        return response()->json([
            'title_become_supplier' => $supplierPage->title_become_supplier,
            'desc_become_supplier' => $this->processMarkdown($supplierPage->desc_become_supplier),
            'why_auto_title' => $supplierPage->why_auto_title,
            'why_auto_desc' => $this->processMarkdown($supplierPage->why_auto_desc),
            'meta_title' => $supplierPage->meta_title,
            'meta_description' => $supplierPage->meta_description,
            'alt_text' => $supplierPage->alt_text,
        ]);
    }
}