<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'username', 'password', 'recovery_code_hash', 'date'
    ];

    public function session() {
        return $this->hasOne(UserSession::class);
    }

    public function favourites() {
        return $this->belongsToMany(Paste::class, 'user_favourites');
    }

    public function pastes() {
        return $this->hasMany(Paste::class);
    }
}

