<?php
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model {

    protected $table = 'user_sessions';

    protected $casts = [
        'expire_at' => 'datetime'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }




}
