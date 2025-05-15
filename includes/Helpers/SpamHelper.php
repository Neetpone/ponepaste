<?php
namespace PonePaste\Helpers;

use PonePaste\Bayes;
use PonePaste\Models\Paste;

class SpamHelper {
    private const FILE_PATH = __DIR__ . '/../../config/spam.json';

    public static function markPaste(Paste $paste, string $category) : void {
        list($bayes, $fp) = self::initBayes();

        $content = $paste->title . "\n" . $paste->plaintextContent();
        $bayes->learn($content, $category);

        fseek($fp, 0);
        fwrite($fp, $bayes->toJson());
        fclose($fp);
    }

    public static function classifyPaste(Paste $paste) : null | string {
        list($bayes, $fp) = self::initBayes();

        $content = $paste->title . "\n" . $paste->plaintextContent();
        $cat = $bayes->categorize($content);

        fclose($fp);

        return $cat;
    }

    private static function initBayes() : array {
        $bayes = new Bayes();

        /* Yes, this could race, but only once in the life of the site. */
        if (!file_Exists(self::FILE_PATH)) {
            file_put_contents(self::FILE_PATH, $bayes->toJson());
        }

        $fp = fopen(self::FILE_PATH, 'r+');
        flock($fp, LOCK_EX);
        $raw_json = fread($fp, filesize(self::FILE_PATH));
        $decoded_json = json_decode($raw_json, true);
        $bayes->fromJson($decoded_json);

        return array($bayes, $fp);
    }
}