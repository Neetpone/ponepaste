<?php
if (php_sapi_name() !== 'cli') {
    header('HTTP/1.1 400 Bad Request');
    die;
}

/* SELECT pastes.id, title, pastes.content, created_at, updated_at,users.username FROM pastes INNER JOIN users ON users.id = pastes.user_id WHERE visible = '0'; */
error_reporting(E_ALL);
ini_set('display_errors', '1');

$PP_ENCRYPTION_KEY = getenv('PP_ENCRYPTION_KEY') ?: '';
$PP_USER = 'ponepaste';
$PP_PASS = getenv('PP_PASS');

if (count($argv) !== 2) {
    echo "usage: {$argv[0]} <outpath>\n";
    exit(1);
}

$outpath = $argv[1];

mkdir($outpath);
mkdir("${outpath}/data/");

$db = new PDO("mysql:host=localhost;dbname=ponepaste_beta;charset=utf8mb4", $PP_USER, $PP_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
    PDO::ATTR_EMULATE_PREPARES => false
]);
$outfile = fopen("{$outpath}/pastes.csv", 'w');
$resp = $db->query("SELECT pastes.id, title, pastes.content, pastes.created_at, pastes.updated_at, users.username
	            	FROM pastes
			INNER JOIN users ON users.id = pastes.user_id
			WHERE pastes.visible = '0'");

$dumped = 0;
$skipped = 0;
$reencoded = 0;

while ($row = $resp->fetch()) {
    list($paste_id, $paste_title, $paste_content,
        $paste_created_at, $paste_updated_at, $paste_author) = $row;

    $paste_content = @openssl_decrypt($paste_content, 'AES-256-CBC', $PP_ENCRYPTION_KEY);
    if ($paste_content === false) {
        fwrite(STDERR, "paste {$paste_id}: failed to decrypt, skipping\n");
        $skipped++;
        continue;
    }

    /* Legacy pastes are raw bytes of an unknown encoding; anything not already UTF-8 is likely Windows-1252. */
    if (!mb_check_encoding($paste_content, 'UTF-8')) {
        $paste_content = mb_convert_encoding($paste_content, 'UTF-8', 'Windows-1252');
        $reencoded++;
    }

    /* Same unescaping the site does in paste.php. */
    $paste_content = htmlspecialchars_decode($paste_content);
    $paste_content = str_replace("\r\n", "\n", $paste_content);

    fputcsv($outfile, [$paste_id, $paste_title, $paste_created_at, $paste_updated_at, $paste_author]);

    $pastefile = fopen("{$outpath}/data/{$paste_id}", 'w');
    fwrite($pastefile, $paste_content);
    fclose($pastefile);
    $dumped++;
}

fclose($outfile);

echo "dumped {$dumped} pastes ({$reencoded} re-encoded from Windows-1252), skipped {$skipped}\n";
