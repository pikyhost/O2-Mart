<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InquiryCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
            ],
            'summary' => $this->when($request->include_summary, $this->getSummary()),
        ];
    }

    private function getSummary(): array
    {
        $items = $this->collection;

        return [
            'total_inquiries' => $items->count(),
            'by_status' => $items->groupBy('status')->map->count(),
            'by_type' => $items->groupBy('type')->map->count(),
            'by_priority' => $items->groupBy('priority')->map->count(),
        ];
    }
}
