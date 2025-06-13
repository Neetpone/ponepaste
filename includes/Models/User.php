<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    public const int ROLE_MODERATOR = 1;
    public const int ROLE_ADMIN = 2;

    protected $table = 'users';
    protected $fillable = [
        'username', 'password', 'recovery_code_hash', 'ip'
    ];

    public function session() {
        return $this->hasOne(UserSession::class);
    }

    public function favourites() {
        return $this->belongsToMany(Paste::class, 'user_favourites')->withPivot('created_at')
            ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))");
    }

    public function pastes() {
        return $this->hasMany(Paste::class)
            ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))");
    }

    public function badges() {
        return $this->hasMany(Badge::class);
    }
}

