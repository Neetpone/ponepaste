<?php
namespace PonePaste\Models;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Paste extends Model {
    public const VISIBILITY_PUBLIC   = 0;
    public const VISIBILITY_UNLISTED = 1;
    public const VISIBILITY_PRIVATE  = 2;

    protected $table = 'pastes';

    protected $guarded = [];
    protected $casts = [
        'visible'    => 'integer',
        'encrypt'    => 'boolean',
        'deleted_at' => 'datetime',
    ];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function deletedBy() {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'paste_taggings');
    }

    public function favouriters() {
        return $this->belongsToMany(User::class, 'user_favourites');
    }

    public function reports() {
        return $this->hasMany(Report::class);
    }

    public function replaceTags(array $tags) : void {
        $this->tags()->detach();

        foreach ($tags as $tagName) {
            $tag = Tag::getOrCreateByName($tagName);
            $this->tags()->attach($tag);
        }

        $this->save();
    }

    public function expiryDisplay() {
        $expiry = $this->expiry;
        if (!$expiry || $expiry === 'NULL') { // TODO: Investigate why this is a string
            return 'Never';
        }

        if ($expiry == 'SELF') {
            return '<b>View Once</b>';
        }
        
        $dateTime = new DateTime();
        $dateTime->setTimestamp($expiry);
        $ret = $dateTime->format('Y-m-d H:i:s');
        if ($dateTime->diff(new DateTime())->days < 1) {
            $ret = "<b>$ret</b>";
        }

        return $ret;
    }

    public static function getRecent(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderBy('created_at', 'DESC')
            ->where('visible', self::VISIBILITY_PUBLIC)
            ->where('is_hidden', false)
            ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))")
            ->limit($count)->get();
    }

    public static function getRecentlyUpdated(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderBy('updated_at', 'DESC')
            ->where('visible', self::VISIBILITY_PUBLIC)
            ->where('is_hidden', false)
            ->where('password', null)
            ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))")
            ->limit($count)->get();
    }

    public static function getMostViewed(int $count = 10) : Collection {
        return Paste::with('user')
            ->orderBy('views')
            ->where('visible', self::VISIBILITY_PUBLIC)
            ->where('is_hidden', false)
            ->where('password', null)
            ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))")
            ->limit($count)->get();
    }

    public static function getMonthPopular(int $count = 10) : Collection {
        return Paste::with('user')
            ->whereRaw('MONTH(created_at) = MONTH(NOW())')
            ->where('visible', self::VISIBILITY_PUBLIC)
            ->where('is_hidden', false)
            ->where('password', null)
            ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))")
            ->orderBy('views')
            ->limit($count)->get();
    }

    public static function getRandom(int $count = 10) : array {
        $total_pastes = Paste::count();
        $pastes = [];

        for ($i = 0; $i < $count; $i++) {
            $pastes[] = self::getSingleRandom($total_pastes);
        }

        return $pastes;
    }

    private static function getSingleRandom(int $total) {
        $paste = null;

        do {
            $paste_id = rand(1, $total);

            $paste = Paste::with('user')
                ->where('visible', self::VISIBILITY_PUBLIC)
                ->where('is_hidden', false)
                ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))")
                ->where('id', $paste_id)
                ->first();
        } while (!$paste);

        return $paste;
    }
}
