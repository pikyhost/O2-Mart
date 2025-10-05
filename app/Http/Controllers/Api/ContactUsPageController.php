<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;

class ContactUsPageController extends Controller
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
    /**
     * Get the contact us page content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contactPage = ContactUs::first();

        if (!$contactPage) {
            return response()->json([
                'message' => 'Contact page content not found',
                'data' => null
            ], 404);
        }

        $data = $contactPage->toArray();
        $data['description'] = $this->processMarkdown($contactPage->description);
        $data['title_desc'] = $this->processMarkdown($contactPage->title_desc);
        $data['form_desc'] = $this->processMarkdown($contactPage->form_desc);
        
        return response()->json([
            'message' => 'Contact page content retrieved successfully',
            'data' => array_merge($data, [
                'image_url' => $contactPage->image_url,
                'meta_title' => $contactPage->meta_title,
                'meta_description' => $contactPage->meta_description,
                'alt_text' => $contactPage->alt_text,
            ])
        ]);

    }
}
