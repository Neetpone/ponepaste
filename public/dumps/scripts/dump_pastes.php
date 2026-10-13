<?php
if (php_sapi_name() !== 'cli') {
    header('HTTP/1.1 400 Bad Request');
    die;
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$PP_ENCRYPTION_KEY = getenv('PP_ENCRYPTION_KEY');
$PP_USER = getenv('PP_USER');
$PP_PASS = getenv('PP_PASS');
$PP_DATABASE_SERVER = getenv('PP_DATABASE_SERVER');
$PP_DATABASE = getenv('PP_DATABASE');

if (count($argv) !== 2) {
    echo "usage: {$argv[0]} <outpath>\n";
    exit(1);
}

if (!$PP_ENCRYPTION_KEY) {
    fwrite(STDERR, "PP_ENCRYPTION_KEY environment variable is not set\n");
    exit(1);
}
if (!$PP_USER) {
    fwrite(STDERR, "PP_USER environment variable is not set\n");
    exit(1);
}
if (!$PP_PASS) {
    fwrite(STDERR, "PP_PASS environment variable is not set\n");
    exit(1);
}
if (!$PP_DATABASE_SERVER) {
    fwrite(STDERR, "PP_DATABASE_SERVER environment variable is not set\n");
    exit(1);
}
if (!$PP_DATABASE) {
    fwrite(STDERR, "PP_DATABASE environment variable is not set\n");
    exit(1);
}

$outpath = $argv[1];

mkdir($outpath);
mkdir("${outpath}/data/");

$db = new PDO("mysql:host={$PP_DATABASE_SERVER};dbname={$PP_DATABASE};charset=utf8mb4", $PP_USER, $PP_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
    PDO::ATTR_EMULATE_PREPARES => false
]);
$outfile = fopen("{$outpath}/pastes.csv", 'w');
$resp = $db->query("SELECT pastes.id, pastes.title, pastes.code, pastes.content, pastes.created_at, pastes.updated_at, users.username,
                (SELECT GROUP_CONCAT(tags.name ORDER BY tags.name SEPARATOR ', ')
                 FROM paste_taggings
                 INNER JOIN tags ON tags.id = paste_taggings.tag_id
                 WHERE paste_taggings.paste_id = pastes.id) AS tags,
                pastes.encrypt
	            	FROM pastes
			INNER JOIN users ON users.id = pastes.user_id
			WHERE pastes.visible < 2 AND (NOT pastes.is_hidden) AND pastes.password is NULL");

$dumped = 0;
$skipped = 0;
$reencoded = 0;

/* Put the header into the CSV file */
fputcsv(stream: $outfile, fields: ['id', 'title', 'format', 'created_at', 'updated_at', 'author', 'tags'], separator: ',', enclosure: '"', escape: "");

while ($row = $resp->fetch()) {
    list($paste_id, $paste_title, $paste_code, $paste_content,
        $paste_created_at, $paste_updated_at, $paste_author, $paste_tags, $paste_encrypt) = $row;

    if ($paste_encrypt) {
        $paste_content = @openssl_decrypt($paste_content, 'AES-256-CBC', $PP_ENCRYPTION_KEY);
        if ($paste_content === false) {
            fwrite(STDERR, "paste {$paste_id}: failed to decrypt, skipping\n");
            $skipped++;
            continue;
        }
    }

    /* skip empty pastes with the title "Removed by moderator" */
    if ($paste_title === "Removed by moderator" && $paste_content === "" && empty($paste_tags)) {
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

    $paste_code = match ($paste_code) {
            'text', 'plaintext' => 'plaintext',
            'pastedown_old', 'pastedown' => 'pastedown',
            default => 'green',
    };

    if ($paste_updated_at === null || $paste_updated_at === '') {
        $paste_updated_at = $paste_created_at;
    }

    fputcsv(stream: $outfile, fields: [$paste_id, $paste_title, $paste_code, $paste_created_at, $paste_updated_at, $paste_author, $paste_tags], separator: ',', enclosure: '"', escape: "");

    $pastefile = fopen("{$outpath}/data/{$paste_id}", 'w');
    fwrite($pastefile, $paste_content);
    fclose($pastefile);
    $dumped++;
}

fclose($outfile);

echo "dumped {$dumped} pastes ({$reencoded} re-encoded from Windows-1252), skipped {$skipped}\n";
