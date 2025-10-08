<?php

namespace App\Console\Commands;

use App\Models\Rim;
use Illuminate\Console\Command;

class FixRimConversions extends Command
{
    protected $signature = 'rims:fix-image-conversions {--rim-id= : Fix specific rim} {--force : Force regenerate all}';
    protected $description = 'Fix missing image conversions for rims';

    public function handle()
    {
        $rimId = $this->option('rim-id');
        $force = $this->option('force');
        
        if ($rimId) {
            $this->fixRim($rimId);
        } else {
            $this->fixAllRims($force);
        }
    }

    private function fixRim($rimId)
    {
        $rim = Rim::find($rimId);
        if (!$rim) {
            $this->error("Rim {$rimId} not found");
            return;
        }

        $media = $rim->getFirstMedia('rim_feature_image');
        if (!$media) {
            $this->warn("No media for rim {$rimId}");
            return;
        }

        $conversions = $media->generated_conversions ?? [];
        if (empty($conversions) || !isset($conversions['large'])) {
            $this->info("Fixing rim {$rimId}...");
            $media->generated_conversions = ['thumb' => true, 'large' => true];
            $media->save();
            $this->info("✓ Fixed rim {$rimId}");
        } else {
            $this->info("Rim {$rimId} already has conversions");
        }
    }

    private function fixAllRims($force)
    {
        $query = Rim::whereHas('media', function($q) {
            $q->where('collection_name', 'rim_feature_image');
        });

        if (!$force) {
            $query->whereHas('media', function($q) {
                $q->where('collection_name', 'rim_feature_image')
                  ->where(function($q2) {
                      $q2->whereNull('generated_conversions')
                         ->orWhere('generated_conversions', '[]')
                         ->orWhere('generated_conversions', '{}');
                  });
            });
        }

        $rims = $query->get();
        $fixed = 0;

        foreach ($rims as $rim) {
            $media = $rim->getFirstMedia('rim_feature_image');
            if ($media) {
                $conversions = $media->generated_conversions ?? [];
                if ($force || empty($conversions) || !isset($conversions['large'])) {
                    $media->generated_conversions = ['thumb' => true, 'large' => true];
                    $media->save();
                    $fixed++;
                    $this->info("✓ Fixed rim {$rim->id}");
                }
            }
        }

        $this->info("Fixed {$fixed} rims");
    }
}