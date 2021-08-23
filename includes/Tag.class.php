<?php

class Tag {
    public int $id;
    public string $name;
    public string $slug;

    public function __construct(array $row) {
        $this->id = (int) $row['id'];
        $this->name = $row['name'];
        $this->slug = $row['slug'];
    }

    public static function getOrCreateByName(DatabaseHandle $conn, string $name) : Tag {
        $name = Tag::cleanTagName($name);

        if ($row = $conn->querySelectOne('SELECT id, name, slug FROM tags WHERE name = ?', [$name])) {
            return new Tag($row);
        }

        $new_slug = Tag::encodeSlug($name);
        $new_tag_id = $conn->queryInsert('INSERT INTO tags (name, slug) VALUES (?, ?)', [$name, $new_slug]);

        return new Tag([
            'id' => $new_tag_id,
            'name' => $name,
            'slug' => $new_slug
        ]);
    }

    public static function findBySlug(DatabaseHandle $conn, string $slug) : Tag|null {
        if ($row = $conn->querySelectOne('SELECT id, name, slug FROM tags WHERE slug = ?', [$slug])) {
            return new Tag($row);
        }

        return null;
    }

    public static function replacePasteTags(DatabaseHandle $conn, int $pasteId, array $tags) {
        $conn->query('DELETE FROM paste_taggings WHERE paste_id = ?', [$pasteId]);

        foreach ($tags as $tagName) {
            $tag = Tag::getOrCreateByName($conn, $tagName);

            $conn->query('INSERT INTO paste_taggings (paste_id, tag_id) VALUES (?, ?)', [$pasteId, $tag->id]);
        }

        // FIXME: We need to get rid of tagsys.
        $conn->query('UPDATE pastes SET tagsys = ? WHERE id = ?', [implode(',', $tags), $pasteId]);
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
                array_push($cleanTags, $cleanName);
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
