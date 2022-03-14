<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Paste extends Model {
    public const VISIBILITY_PUBLIC   = 0;
    public const VISIBILITY_UNLISTED = 1;
    public const VISIBILITY_PRIVATE  = 2;

    protected $table = 'pastes';

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'paste_taggings');
    }

    public function favouriters() {
        return $this->belongsToMany(User::class, 'user_favourites');
    }

    public function replaceTags(array $tags) {
        $this->tags()->detach();

        foreach ($tags as $tagName) {
            $tag = Tag::getOrCreateByName($tagName);
            $this->tags()->attach($tag);
        }

        $this->save();
    }

    public static function getRecent(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderBy('created_at', 'DESC')
            ->where('visible', 0)
            ->limit($count)->get();
    }

    public static function getRecentlyUpdated(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderBy('updated_at', 'DESC')
            ->where('visible', 0)
            ->limit($count)->get();
    }

    public static function getMostViewed(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderBy('views')
            ->where('visible', 0)
            ->limit($count)->get();
    }

    public static function getMonthPopular(int $count = 10) : Collection {
        return Paste::with('user')
            ->whereRaw('MONTH(created_at) = MONTH(NOW())')
            ->where('visible', 0)
            ->orderBy('views')
            ->limit($count)->get();
    }

    public static function getRandom(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderByRaw('RAND()')
            ->where('visible', 0)
            ->limit($count)->get();
    }
}
