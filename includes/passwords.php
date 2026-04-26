<?php
require_once(__DIR__ . '/config.php');

function pp_password_hash(string $password) : string {
    return 'P' . password_hash($password . PP_PASSWORD_PEPPER, PASSWORD_BCRYPT);
}

function pp_password_verify(string $password, string $hash, ?bool &$needs_rehash = null) : bool {
    /* New, peppered hash. */
    if ($hash && $hash[0] === 'P') {
        if ($needs_rehash !== null) {
            $needs_rehash = false;
        }

        return password_verify($password . PP_PASSWORD_PEPPER, substr($hash, 1));
    }

    /* Old, non-peppered hash. */
    if ($needs_rehash !== null) {
        $needs_rehash = true;
    }

    return password_verify($password, $hash);
}

function pp_random_bytes(int $length) : string {
    try {
        return random_bytes($length);
    } catch (Exception) {
        /* Out of entropy! */
        die('Error generating random bytes - this should never be seen!');
    }
}

function pp_random_token() : string {
    return bin2hex(pp_random_bytes(64));
}

function pp_random_password() : string {
    return bin2hex(pp_random_bytes(16));
}

function pp_random_friendly_token($size = 6) : string {
    $path = '/usr/share/dict/words';

    if (!file_exists($path)) {
        trigger_error('Friendly token word file does not exist; falling back to using an unfriendly token.', E_USER_WARNING);

        return pp_random_token();
    }

    $words = pp_random_lines_from_file($path, $size);
    $joined = implode(' ', $words);

    return preg_replace('/[^a-z ]/', '', strtolower($joined));
}
