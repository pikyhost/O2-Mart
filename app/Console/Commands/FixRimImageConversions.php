<?php

namespace App\Console\Commands;

use App\Models\Rim;
use Illuminate\Console\Command;

class FixRimImageConversions extends Command
{
    protected $signature = 'rim:fix-image-conversions {--id= : Specific rim ID to fix}';
    protected $description = 'Fix missing image conversions for rims';

    public function handle()
    {
        $rimId = $this->option('id');
        
        if ($rimId) {
            $this->fixSingleRim($rimId);
        } else {
            $this->fixAllRims();
        }
    }

    private function fixSingleRim($rimId)
    {
        $rim = Rim::find($rimId);
        if (!$rim) {
            $this->error("Rim with ID {$rimId} not found");
            return;
        }

        $this->info("Fixing rim ID: {$rimId} - {$rim->name}");
        $this->fixRimConversions($rim);
    }

    private function fixAllRims()
    {
        $this->info('Checking all rims for missing conversions...');
        
        $rims = Rim::whereHas('media', function($query) {
            $query->where('collection_name', 'rim_feature_image');
        })->get();

        $fixed = 0;
        $total = $rims->count();

        foreach ($rims as $rim) {
            if ($this->fixRimConversions($rim)) {
                $fixed++;
            }
        }

        $this->info("Fixed {$fixed} out of {$total} rims");
    }

    private function fixRimConversions($rim)
    {
        $media = $rim->getFirstMedia('rim_feature_image');
        if (!$media) {
            $this->warn("No media found for rim ID: {$rim->id}");
            return false;
        }

        $conversions = $media->getGeneratedConversions();
        $needsFix = false;

        // Check if large conversion exists and file is present
        if (!isset($conversions['large']) || !$conversions['large']) {
            $needsFix = true;
            $this->warn("Missing large conversion for rim ID: {$rim->id}");
        } else {
            try {
                $largePath = $media->getPath('large');
                if (!file_exists($largePath)) {
                    $needsFix = true;
                    $this->warn("Large conversion file missing for rim ID: {$rim->id}");
                }
            } catch (\Exception $e) {
                $needsFix = true;
                $this->warn("Error checking large conversion for rim ID: {$rim->id} - " . $e->getMessage());
            }
        }

        if ($needsFix) {
            try {
                $this->info("Regenerating conversions for rim ID: {$rim->id}");
                $media->performConversions();
                $this->info("✓ Fixed rim ID: {$rim->id}");
                return true;
            } catch (\Exception $e) {
                $this->error("Failed to fix rim ID: {$rim->id} - " . $e->getMessage());
                return false;
            }
        }

        return false;
    }
}