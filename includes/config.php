<?php
define("PP_DEBUG", (gethostname() === 'thunderlane'));
if (PP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/* Maximum paste size in bytes */
const PP_PASTE_LIMIT_BYTES = 1048576;

/* A long and random string used for additionally salting passwords. */
const PP_PASSWORD_PEPPER = 'd791b6c6-91f2-4e8f-ba80-74ea968e4931';

/* Whether to use friendly URLs that require mod_rewrite */
const PP_MOD_REWRITE = true;

/* Redis credentials */
const PP_REDIS_HOST = '127.0.0.1';
const PP_REDIS_DB = 'ponepaste';

$db_host = 'localhost';
$db_schema = 'p0nepast3s';
$db_user = 'P0nedbAcc0unt';
$db_pass = '1NWO6Tp17IFz9lbl';

// I'm sorry, I didn't want to edit this file and check it in, but I may need to make other changes to it, so I did this
if (PP_DEBUG) {
    $db_host = 'localhost';
    $db_schema = 'ponepaste';
    $db_user = 'ponepaste';
    $db_pass = 'ponepaste';
}


// Secret key for paste encryption
//$sec_key = "8ac67343e7980b16b31e8311d4377bbb";
const PP_ENCRYPTION_ALGO = 'AES-256-CBC';
const PP_ENCRYPTION_KEY = '';

const PP_HIGHLIGHT_FORMATS = [
    'green' => 'Green Text',
    'text' => 'Plain Text',
    'pastedown' => 'pastedown',
    'pastedown_old' => 'pastedown old'
];


// Cookie - I want a cookie, can I have one?
