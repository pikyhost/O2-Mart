<?php

namespace App\Console\Commands;

use App\Models\Rim;
use Illuminate\Console\Command;

class FixRimImageConversions extends Command
{
    protected $signature = 'rim:fix-image-conversions {--id= : Specific rim ID to fix} {--from= : Fix rims starting from this ID}';
    protected $description = 'Fix missing image conversions for rims';

    public function handle()
    {
        $rimId = $this->option('id');
        $fromId = $this->option('from');
        
        if ($rimId) {
            $this->fixSingleRim($rimId);
        } elseif ($fromId) {
            $this->fixRimsFrom($fromId);
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

    private function fixRimsFrom($fromId)
    {
        $this->info("Checking rims from ID {$fromId} for missing conversions...");
        
        $rims = Rim::where('id', '>=', $fromId)
            ->whereHas('media', function($query) {
                $query->where('collection_name', 'rim_feature_image');
            })->get();

        $fixed = 0;
        $total = $rims->count();

        foreach ($rims as $rim) {
            if ($this->fixRimConversions($rim)) {
                $fixed++;
            }
        }

        $this->info("Fixed {$fixed} out of {$total} rims (from ID {$fromId})");
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
                
                // Delete existing conversions and regenerate
                $media->clearMediaConversions();
                
                // Manually create conversions
                $thumbPath = $media->getPath('thumb');
                $largePath = $media->getPath('large');
                
                // Create directories if they don't exist
                $conversionDir = dirname($largePath);
                if (!file_exists($conversionDir)) {
                    mkdir($conversionDir, 0755, true);
                }
                
                // Generate thumb conversion
                if (extension_loaded('gd') || extension_loaded('imagick')) {
                    $originalPath = $media->getPath();
                    if (file_exists($originalPath)) {
                        // Create thumb (300x300)
                        $this->createImageConversion($originalPath, $thumbPath, 300, 300);
                        // Create large (800x800)
                        $this->createImageConversion($originalPath, $largePath, 800, 800);
                        
                        // Update generated conversions in database
                        $media->generated_conversions = ['thumb' => true, 'large' => true];
                        $media->save();
                    }
                }
                
                $this->info("✓ Fixed rim ID: {$rim->id}");
                return true;
            } catch (\Exception $e) {
                $this->error("Failed to fix rim ID: {$rim->id} - " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    private function createImageConversion($sourcePath, $targetPath, $width, $height)
    {
        if (extension_loaded('gd')) {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) return false;
            
            $sourceWidth = $imageInfo[0];
            $sourceHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Create source image
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                default:
                    return false;
            }
            
            if (!$sourceImage) return false;
            
            // Calculate dimensions maintaining aspect ratio
            $ratio = min($width / $sourceWidth, $height / $sourceHeight);
            $newWidth = intval($sourceWidth * $ratio);
            $newHeight = intval($sourceHeight * $ratio);
            
            // Create target image
            $targetImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($mimeType === 'image/png') {
                imagealphablending($targetImage, false);
                imagesavealpha($targetImage, true);
            }
            
            // Resize
            imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
            
            // Save
            $result = imagejpeg($targetImage, $targetPath, 90);
            
            // Cleanup
            imagedestroy($sourceImage);
            imagedestroy($targetImage);
            
            return $result;
        }
        
        return false;
    }
}