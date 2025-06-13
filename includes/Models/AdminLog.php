<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model {
    public const int ACTION_LOGIN = 0;
    public const int ACTION_FAIL_LOGIN = 1;
    public const int ACTION_EDIT_CONFIG = 2;
    public const int ACTION_HIDE_PASTE = 3;
    public const int ACTION_BLANK_PASTE = 4;

    public const int ACTION_MARK_PASTE = 5;

    public const array ACTION_NAMES = [
        'Login',
        'Failed Login',
        'Edit Config',
        'Hide Paste',
        'Blank Paste',
        'Mark Paste',
    ];

    protected $table = 'admin_logs';
    protected $fillable = ['user_id', 'action', 'ip', 'time', 'message'];

    public $timestamps = false;

    public function user() {
        return $this->belongsto(User::class);
    }

    public static function updateAdminHistory(User $admin, int $action, ?string $message = null) : void {
        $log = new AdminLog([
            'user_id' => $admin->id,
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'message' => $message
        ]);

        $log->save();
    }
}
