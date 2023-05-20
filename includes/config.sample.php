<?php
const PP_DEBUG = false;

if (PP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/* Maximum paste size in bytes */
const PP_PASTE_LIMIT_BYTES = 1048576;

/* A long and random string used for additionally salting passwords. */
const PP_PASSWORD_PEPPER = 'a long and secure random string here';

/* Whether to use friendly URLs that require mod_rewrite */
const PP_MOD_REWRITE = true;

/* Redis credentials */
const PP_REDIS_HOST = '127.0.0.1';
const PP_REDIS_DB = 'ponepaste';

$db_host = 'localhost';
$db_schema = 'ponepaste';
$db_user = 'ponepaste';
$db_pass = 'ponepaste';

// Secret key for paste encryption
const PP_ENCRYPTION_ALGO = 'AES-256-CBC';
const PP_ENCRYPTION_KEY = 'a long and secure random string here';

const PP_HIGHLIGHT_FORMATS = [
    'green' => 'Green Text',
    'text' => 'Plain Text',
    'pastedown' => 'pastedown',
    'pastedown_old' => 'pastedown old'
];

// Cookie - I want a cookie, can I have one?
