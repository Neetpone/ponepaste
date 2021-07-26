<?php

class User {
    public const REMEMBER_TOKEN_COOKIE = '_ponepaste_token';

    public int $user_id;
    public string $username;

    private function __construct(array $row) {
        $this->user_id = intval($row['id']);
        $this->username = $row['username'];
    }

    public function destroySession(DatabaseHandle $conn, string $token) {
        $conn->query('DELETE FROM user_sessions WHERE user_id = ? AND token = ?', [$this->user_id, $token]);
    }

    public static function findByUsername(DatabaseHandle $conn, string $username) : User|null {
        $query = $conn->query('SELECT id, username FROM users WHERE username = ?', [$username]);
        $row = $query->fetch();

        return empty($row) ? null : new User($row);
    }

    public static function current(DatabaseHandle $conn) : User|null {
        $session_user = User::createFromPhpSession($conn);

        if ($session_user !== null) {
            return $session_user;
        }

        if (!empty($_COOKIE[self::REMEMBER_TOKEN_COOKIE]) &&
            ($token_user = User::createFromRememberToken($conn, $_COOKIE[self::REMEMBER_TOKEN_COOKIE]))) {
            $_SESSION['user_id'] = $token_user->user_id;
            return $token_user;
        }

        return null;
    }

    public static function createFromRememberToken(DatabaseHandle $conn, string $remember_token) : User|null {
        $result = $conn->query(
            'SELECT users.id AS id, users.username AS username, users.banned AS banned, user_sessions.id AS session_id, user_sessions.expire_at AS session_expiry
                FROM user_sessions
                INNER JOIN users ON users.id = user_sessions.user_id
                WHERE user_sessions.token = ?', [$remember_token]
        );

        if ($row = $result->fetch()) {
            $session_expiry = new DateTime($row['session_expiry']);
            $now = new DateTime();

            /* Session is expired (diff is negative) */
            if ($now->diff($session_expiry)->invert === 1) {
                $conn->query('DELETE FROM user_sessions WHERE id = ?', [$row['session_id']]);
                return null;
            }

            return new User($row);
        }

        return null;
    }

    public static function createFromPhpSession(DatabaseHandle $conn) : User|null {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $user_id = intval($_SESSION['user_id']);

        $row = $conn->query('SELECT id, username, banned FROM users WHERE id = ?', [$user_id])->fetch();

        return $row ? new User($row) : null;
    }
}
