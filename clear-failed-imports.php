<?php

// Clear failed import rows
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear failed import rows
DB::table('failed_import_rows')->truncate();
echo "Failed import rows cleared successfully.\n";