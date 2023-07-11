<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model {
    public const ACTION_LOGIN = 0;
    public const ACTION_FAIL_LOGIN = 1;
    public const ACTION_EDIT_CONFIG = 2;
    public const ACTION_HIDE_PASTE = 3;
    public const ACTION_BLANK_PASTE = 4;

    public const ACTION_NAMES = [
        'Login',
        'Failed Login',
        'Edit Config',
        'Hide Paste',
        'Blank Paste'
    ];

    protected $table = 'admin_logs';
    protected $fillable = ['user_id', 'action', 'ip', 'time', 'message'];

    public $timestamps = false;

    public function user() {
        return $this->belongsto(User::class);
    }

    public static function updateAdminHistory(User $admin, int $action, string $message = null) : void {
        $log = new AdminLog([
            'user_id' => $admin->id,
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'message' => $message
        ]);

        $log->save();
    }
}
