<?php

class User {
    public int $user_id;
    public string $username;

    private function __construct(array $row) {
        $this->user_id = intval($row['user_id']);
        $this->username = $row['username'];
    }

    public static function current(DatabaseHandle $conn) : User | null {
        $session_user = User::createFromPhpSession($conn);

        if ($session_user !== null) {
            return $session_user;
        }

        if (!empty($_COOKIE['_ponepaste_token']) &&
            ($token_user = User::createFromRememberToken($conn, $_COOKIE['_ponepaste_token']))) {
            $_SESSION['user_id'] = $token_user->user_id;
            return $token_user;
        }

        return null;
    }

    public static function createFromRememberToken(DatabaseHandle $conn, string $remember_token) : User | null {
        $result = $conn->query(
            'SELECT users.id AS id, users.username AS username
                FROM user_sessions
                INNER JOIN users ON users.id = user_sessions.user_id
                WHERE user_sessions.token = ?', [$remember_token]
        );

        if ($row = $result->fetch()) {
            return new User($row);
        }

        return null;
    }

    public static function createFromPhpSession(DatabaseHandle $conn) : User | null {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $user_id = intval($_SESSION['user_id']);

        $row = $conn->query('SELECT id, username FROM users WHERE id = ?', [$user_id])->fetch();

        return $row ? new User($row) : null;
    }
}
