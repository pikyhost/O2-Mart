<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ImportProgressController extends Controller
{
    public function getProgress($importId)
    {
        $progress = Cache::get("import_progress_{$importId}", [
            'processed' => 0,
            'total' => 0,
            'percentage' => 0,
            'status' => 'not_found'
        ]);

        return response()->json($progress);
    }
}