<?php
/*
 * $ID Project: Paste 2.0 - J.Samuel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in LIC.txt for more details.
 */
if (gethostname() === 'thunderlane') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/* Maximum paste size in bytes */
const PP_PASTE_LIMIT_BYTES = 1048576;

/* A long and random string used for additionally salting passwords. */
const PP_PASSWORD_PEPPER = 'd791b6c6-91f2-4e8f-ba80-74ea968e4931';

/* Whether to use friendly URLs that require mod_rewrite */
const PP_MOD_REWRITE = true;

$db_host = 'localhost';
$db_schema = 'p0nepast3s';
$db_user = 'P0nedbAcc0unt';
$db_pass = '1NWO6Tp17IFz9lbl';

// I'm sorry, I didn't want to edit this file and check it in, but I may need to make other changes to it, so I did this
if (gethostname() === 'thunderlane') {
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
