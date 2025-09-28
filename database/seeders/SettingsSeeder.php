<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Define filenames
        $logo = 'logo_1.png';
        $darkLogo = 'logo_2.png';

        // Define source and destination
        $sourceDir = public_path('images');
        $destDir = storage_path('app/public/settings');

        // Ensure destination directory exists
        File::ensureDirectoryExists($destDir);

        // Copy images if they exist
        if (File::exists("$sourceDir/$logo")) {
            File::copy("$sourceDir/$logo", "$destDir/$logo");
        }

        if (File::exists("$sourceDir/$darkLogo")) {
            File::copy("$sourceDir/$darkLogo", "$destDir/$darkLogo");
        }

        // Seed the database
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'site_name'   => 'Auto Mart',
                'country_id'  => null,
                'currency_id' => null,
                'logo'        => "settings/$logo",
                'dark_logo'   => "settings/$darkLogo",
                'favicon'     => null,
                'phone'       => null,
                'email'       => null,
                'facebook'    => null,
                'youtube'     => null,
                'instagram'   => null,
                'x'           => null,
                'snapchat'    => null,
                'tiktok'      => null,
            ]
        );
    }
}
