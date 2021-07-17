<?php
require_once(__DIR__ . '/config.php');

function pp_password_hash(string $password) : string {
    return password_hash($password . PP_PASSWORD_PEPPER, PASSWORD_BCRYPT);
}

function pp_password_verify(string $password, string $hash, bool &$needs_rehash) : bool {
    /* New, peppered hash. */
    if ($hash[0] === 'P') {
        $needs_rehash = false;
        return password_verify($password . PP_PASSWORD_PEPPER, substr($hash, 1));
    }

    /* Old, unpeppered hash. */

    $needs_rehash = true;
    return password_verify($password, $hash);
}
