<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model {
    public const ACTION_LOGIN = 0;
    public const ACTION_FAIL_LOGIN = 1;
    public const ACTION_EDIT_CONFIG = 2;

    public const ACTION_NAMES = [
        'Login',
        'Failed Login',
        'Edit Config'
    ];

    protected $table = 'admin_logs';
    protected $fillable = ['user_id', 'action', 'ip', 'time'];

    public $timestamps = false;

    public function user() {
        return $this->belongsto(User::class);
    }
}
