<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model {
    protected $table = 'user_sessions';

    protected $casts = [
        'expire_at' => 'datetime'
    ];

    protected $fillable = [
        'user_id', 'token', 'expire_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
