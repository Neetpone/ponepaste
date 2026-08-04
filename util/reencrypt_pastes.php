<?php
/* Encrypts every paste still stored as plaintext. */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../includes/config.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use PonePaste\Models\Paste;

error_reporting(E_ALL);
ini_set('display_errors', '1');

$commit = in_array('--commit', $argv, true);

if (!$commit) {
    echo "DRY RUN - pass --commit to write changes.\n";
}

$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $db_host,
    'database' => $db_schema,
    'username' => $db_user,
    'password' => $db_pass,
    'charset' => 'utf8mb4',
    'prefix' => ''
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$converted = 0;
$failed = 0;

Paste::where(function ($query) {
    $query->where('encrypt', '!=', 1)->orWhereNull('encrypt');
})->chunkById(200, function ($pastes) use ($commit, &$converted, &$failed) {
    foreach ($pastes as $paste) {
        $plaintext = $paste->content;
        $encrypted = @openssl_encrypt($plaintext, PP_ENCRYPTION_ALGO, PP_ENCRYPTION_KEY);

        if ($encrypted === false) {
            fwrite(STDERR, "paste {$paste->id}: encryption failed, skipping\n");
            $failed++;
            continue;
        }

        if (@openssl_decrypt($encrypted, PP_ENCRYPTION_ALGO, PP_ENCRYPTION_KEY) !== $plaintext) {
            fwrite(STDERR, "paste {$paste->id}: round trip mismatch, skipping\n");
            $failed++;
            continue;
        }

        if ($commit) {
            $paste->content = $encrypted;
            $paste->encrypt = true;
            $paste->save();
        }

        $converted++;
    }
});

echo "converted {$converted} pastes, {$failed} failed\n";
