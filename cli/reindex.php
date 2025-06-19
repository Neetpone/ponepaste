#!/usr/bin/env php
<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Helpers\SearchHelper;
use PonePaste\Models\Paste;

// Default chunk size
$chunk_size = 50;

// Parse command line arguments
$options = getopt('', ['chunk-size::']);
if (isset($options['chunk-size'])) {
    $chunk_size = (int)$options['chunk-size'];
    if ($chunk_size < 1) {
        echo "Error: Chunk size must be greater than 0\n";
        exit(1);
    }
}

echo "Starting reindex process with chunk size: {$chunk_size}\n";

// Drop and recreate the index
echo "Dropping existing index...\n";
SearchHelper::instance()->dropPasteIndex();
echo "Creating new index...\n";
SearchHelper::instance()->createPasteIndex();

// Get total count for progress tracking
$total_pastes = Paste::count();
echo "Found {$total_pastes} pastes to index\n";

$processed = 0;
$start_time = microtime(true);

// Process in chunks
Paste::chunk($chunk_size, function($models) use (&$processed, $total_pastes, $start_time) {
    foreach ($models as $model) {
        SearchHelper::instance()->indexPaste($model);
        $processed++;
        
        // Calculate progress and ETA
        $progress = ($processed / $total_pastes) * 100;
        $elapsed = microtime(true) - $start_time;
        $rate = $processed / $elapsed;
        $remaining = ($total_pastes - $processed) / $rate;
        
        echo sprintf(
            "\rProgress: %.1f%% (%d/%d) - Rate: %.1f pastes/sec - ETA: %.1f seconds",
            $progress,
            $processed,
            $total_pastes,
            $rate,
            $remaining
        );
    }
});

echo "\nReindexing completed!\n";
echo "Total time: " . round(microtime(true) - $start_time, 2) . " seconds\n";
