<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MarkdownAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,txt|max:5120',
        ]);

        $path = $request->file('file')->store('blog-content', 'public');

        return response()->json([
            'url' => asset('storage/'.$path),
        ]);
    }
}
