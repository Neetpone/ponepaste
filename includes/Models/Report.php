<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    protected  $table = 'reports';
    protected $fillable = [
        'paste_id',
        'user_id',
        'reason',
        'open'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paste() {
        return $this->belongsTo(Paste::class);
    }
}
