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
        // Clean up and format the content properly
        $lines = explode("\n", $content);
        $processedLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Handle bold formatting
            $line = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $line);
            
            // Skip empty lines
            if (empty($line)) {
                $processedLines[] = '';
                continue;
            }
            
            $processedLines[] = $line;
        }
        
        // Join back and process with markdown
        $content = implode("\n", $processedLines);
        
        return Str::markdown($content);
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