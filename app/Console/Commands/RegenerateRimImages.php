<?php

namespace App\Console\Commands;

use App\Models\Rim;
use Illuminate\Console\Command;

class RegenerateRimImages extends Command
{
    protected $signature = 'rims:regenerate-images';
    protected $description = 'Regenerate all rim image conversions';

    public function handle()
    {
        $this->info('Starting rim image regeneration...');
        
        Rim::whereHas('media')->chunk(100, function ($rims) {
            foreach ($rims as $rim) {
                $media = $rim->getFirstMedia('rim_feature_image');
                if ($media) {
                    \Artisan::call('media-library:regenerate', ['--ids' => $media->id]);
                    $this->line("Regenerated: Rim ID {$rim->id}");
                }
            }
        });
        
        $this->info('All rim images regenerated!');
    }
}