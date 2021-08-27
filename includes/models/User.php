<?php
use Illuminate\Database\Eloquent\Model;
require_once(__DIR__  . '/Paste.php');

class User extends Model {
    protected $table = 'users';

    public function session() {
        return $this->hasOne(UserSession::class);
    }

    public function favourites() {
        return $this->belongsToMany(Paste::class, 'user_favourites');
    }

    /*public function pastes() {
        return $this->hasMany(Paste::class);
    }*/


}

