<?php
namespace PonePaste\Helpers;

use DateTime;
use PonePaste\Models\User;
use PonePaste\Models\UserSession;

class SessionHelper {
    public const REMEMBER_TOKEN_COOKIE = '_ponepaste_token';
    public const CSRF_TOKEN_KEY = 'csrf_token';

    public static function currentUser() {
        $session_user = SessionHelper::currentUserFromPhpSession();

        if ($session_user !== null) {
            return $session_user;
        }

        if (!empty($_COOKIE[self::REMEMBER_TOKEN_COOKIE]) &&
            ($session = SessionHelper::currentUserFromRememberToken($_COOKIE[self::REMEMBER_TOKEN_COOKIE]))) {
            $_SESSION['user_id'] = $session->user_id;
            return $session;
        }

        return null;
    }

    public static function destroySession() {
        $token = $_COOKIE[SessionHelper::REMEMBER_TOKEN_COOKIE];

        UserSession::where('token', $token)->delete();

        unset($_COOKIE[SessionHelper::REMEMBER_TOKEN_COOKIE]);
        setcookie(SessionHelper::REMEMBER_TOKEN_COOKIE, null, time() - 3600);
    }

    private static function currentUserFromRememberToken(string $remember_token) {
        $session = UserSession
            ::with('user')
            ->where('token', $remember_token)
            ->first();

        if (!$session) {
            return null;
        }

        $session_expiry = $session->expire_at;
        $now = new DateTime();

        /* Session is expired (diff is negative) */
        if ($now->diff($session_expiry)->invert === 1) {
            $session->delete();
            return null;
        }

        return $session->user;
    }

    private static function currentUserFromPhpSession() {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        return User::find(intval($_SESSION['user_id']));
    }
}