<?php
// Debug commands for tinker - copy and paste these one by one

// 1. Check if inquiry exists and has media
$inquiry = App\Models\Inquiry::latest()->first();
echo "Latest Inquiry ID: " . $inquiry->id . "\n";

// 2. Check media collections
echo "Media collections: \n";
print_r($inquiry->getMediaCollections()->pluck('name')->toArray());

// 3. Check all media for this inquiry
echo "All media count: " . $inquiry->getMedia()->count() . "\n";

// 4. Check car license photos
echo "Car license photos count: " . $inquiry->getMedia('car_license_photos')->count() . "\n";
$inquiry->getMedia('car_license_photos')->each(function($media) {
    echo "File: " . $media->file_name . " - URL: " . $media->getUrl() . "\n";
});

// 5. Check part photos
echo "Part photos count: " . $inquiry->getMedia('part_photos')->count() . "\n";
$inquiry->getMedia('part_photos')->each(function($media) {
    echo "File: " . $media->file_name . " - URL: " . $media->getUrl() . "\n";
});

// 6. Check media table directly
echo "Media table records for inquiry: \n";
$mediaRecords = DB::table('media')->where('model_type', 'App\Models\Inquiry')->where('model_id', $inquiry->id)->get();
print_r($mediaRecords->toArray());