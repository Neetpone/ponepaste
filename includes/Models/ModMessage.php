<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class ModMessage extends Model {
    protected $table = 'mod_messages';
    protected $fillable = ['user_id', 'message'];

    public function user() {
        return $this->belongsto(User::class);
    }
}
