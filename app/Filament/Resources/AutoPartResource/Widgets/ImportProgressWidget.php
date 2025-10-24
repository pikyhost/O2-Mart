<?php

namespace App\Filament\Resources\AutoPartResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class ImportProgressWidget extends Widget
{
    protected static string $view = 'filament.widgets.import-progress';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1;
    
    // Enable polling every 500ms for ultra real-time updates
    protected static ?string $pollingInterval = '500ms';
    
    // Disable caching for real-time updates
    protected static bool $isLazy = false;
    
    public array $activeImports = [];
    public bool $hasActiveImports = false;
    
    public static function canView(): bool
    {
        // Always show the widget - it will hide itself if no active imports
        return true;
    }
    
    public function mount(): void
    {
        $this->loadActiveImports();
    }
    
    #[On('refresh-import-progress')]
    public function loadActiveImports(): void
    {
        // Clear any cached data
        $this->activeImports = [];
        $this->hasActiveImports = false;
        
        // Force fresh query with no cache
        \DB::connection()->disableQueryLog();
        
        // Get ALL importer imports (active and recent completed) - not just AutoPartImporter
        $activeImportRecords = \Filament\Actions\Imports\Models\Import::query()
            ->where(function ($query) {
                // Active imports (not completed)
                $query->whereNull('completed_at')
                    ->where('created_at', '>=', now()->subHours(24));
            })
            ->orWhere(function ($query) {
                // Recently completed imports (last 5 minutes)
                $query->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->subMinutes(5));
            })
            ->orderBy('created_at', 'desc')
            ->limit(1) // Only show the most recent import
            ->get();
            
        foreach ($activeImportRecords as $import) {
            $progressData = $this->getImportProgress($import);
            if ($progressData) {
                $this->activeImports[] = $progressData;
            }
        }
        
        $this->hasActiveImports = !empty($this->activeImports);
    }
    
    protected function getImportProgress($import): ?array
    {
        // Force fresh data from database - no cache
        $import = \Filament\Actions\Imports\Models\Import::find($import->id);
        
        if (!$import) {
            return null;
        }
        
        // Calculate progress from database (get fresh data)
        $totalRows = $import->total_rows ?? 0;
        $processedRows = $import->processed_rows ?? 0;
        $successfulRows = $import->successful_rows ?? 0;
        $failedRows = $import->getFailedRowsCount();
        
        $percentage = $totalRows > 0 
            ? round(($processedRows / $totalRows) * 100, 1)
            : 0;
            
        return [
            'import_id' => $import->id,
            'file_name' => $import->file_name ?? 'Unknown',
            'importer' => class_basename($import->importer) ?? 'AutoPartImporter',
            'total' => $totalRows,
            'processed' => $processedRows,
            'successful' => $successfulRows,
            'failed' => $failedRows,
            'percentage' => $percentage,
            'status' => $import->completed_at ? 'completed' : 'processing',
            'started_at' => $import->created_at->format('H:i:s'),
            'elapsed_time' => $import->created_at->diffForHumans(null, true),
            'estimated_remaining' => $this->estimateRemainingTime($import, $processedRows, $totalRows),
        ];
    }
    
    protected function estimateRemainingTime($import, $processed, $total): ?string
    {
        if ($processed == 0 || $total == 0) {
            return null;
        }
        
        $elapsedSeconds = $import->created_at->diffInSeconds(now());
        $rowsPerSecond = $processed / max($elapsedSeconds, 1);
        $remainingRows = $total - $processed;
        $remainingSeconds = $rowsPerSecond > 0 ? $remainingRows / $rowsPerSecond : 0;
        
        if ($remainingSeconds < 60) {
            return round($remainingSeconds) . 's';
        } elseif ($remainingSeconds < 3600) {
            return round($remainingSeconds / 60) . 'm';
        } else {
            return round($remainingSeconds / 3600, 1) . 'h';
        }
    }
}