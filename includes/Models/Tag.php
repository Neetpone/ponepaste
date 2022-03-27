<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    protected $table = 'tags';
    protected $fillable = ['name', 'slug'];
    public $timestamps = false;

    public static function getOrCreateByName(string $name) : Tag {
        $name = Tag::cleanTagName($name);

        if ($tag = Tag::where('name', $name)->first()) {
            return $tag;
        }

        return Tag::create([
            'name' => $name,
            'slug' => Tag::encodeSlug($name)
        ]);
    }

    /**
     * Normalize a tag name, which involves downcasing it, normalizing smart quotes, trimming the string, and
     * normalizing runs of whitespace to a single space.
     *
     * @param string $name User-input tag name, for example "I'm    gay  ".
     * @return string Cleaned tag name, for example "i'm gay".
     */
    public static function cleanTagName(string $name) : string {
        /* Downcase */
        $name = trim(strtolower($name));

        /* Smart quotes to regular quotes */
        $name = preg_replace("[\u{00b4}\u{2018}\u{2019}\u{201a}\u{201b}\u{2032}]", "'", $name);
        $name = preg_replace("[\u{00b4}\u{201c}\u{201d}\u{201e}\u{201f}\u{2033}]", '"', $name);

        /* Collapse whitespace */
        return preg_replace('/\s+/', ' ', $name);
    }

    public static function parseTagInput(string $tagInput) : array {
        $cleanTags = [];

        foreach (explode(',', $tagInput) as $tagName) {
            $cleanName = Tag::cleanTagName($tagName);

            if (!empty($cleanName)) {
                $cleanTags[] = $cleanName;
            }
        }

        return array_unique($cleanTags);
    }

    private static function encodeSlug(string $name) : string {
        /* This one's a doozy. */
        $name = str_replace(
            ['-', '/', '\\', ':', '.', '+'],
            ['-dash-', '-fwslash-', '-bwslash-', '-colon-', '-dot-', '-plus-'],
            $name
        );

        /* urlencode it. for URLs, dipshit. */
        return str_replace('%20', '+', urlencode($name));
    }
}