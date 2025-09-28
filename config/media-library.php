<?php

return [

    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * Maximum file size for uploads (in bytes).
     */
    'max_file_size' => 1024 * 1024 * 100, // 100MB

    /*
     * Media model class
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

];
