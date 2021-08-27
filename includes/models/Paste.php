<?php
use Illuminate\Database\Eloquent\Model;

require_once(__DIR__ . '/Tag.php');

class Paste extends Model {
    protected $table = 'pastes';

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'paste_taggings');
    }
}
