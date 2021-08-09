<?php
require_once(__DIR__ . '/config.php');

function pp_password_hash(string $password) : string {
    return 'P' . password_hash($password . PP_PASSWORD_PEPPER, PASSWORD_BCRYPT);
}

function pp_password_verify(string $password, string $hash, bool &$needs_rehash = null) : bool {
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
    return hash('SHA512', pp_random_bytes(64));
}

function pp_random_password() : string {
    /* MD-5 is OK to use here because it is not being used to protect secure data,
     * but rather to reduce the size of the string a little into something that
     * can reasonably be handled by a user.
     */
    return hash('MD5', pp_random_bytes(64));
}