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
 
$currentversion = 2.2;

// Max paste size in MB. This value should always be below the value of
// post_max_size in your PHP configuration settings (php.ini) or empty errors will occur.
// The value we got on installation of Paste was: post_max_size = 128M
// Otherwise, the maximum value that can be set is 4000 (4GB)
$pastelimit = "1"; // 0.5 = 512 kilobytes, 1 = 1MB
 
// OAUTH (to enable, change to yes and edit)
$enablefb = "no";
$enablegoog = "no";

// "CHANGE THIS" = Replace with your details
// Facebook
define('FB_APP_ID', 'CHANGE THIS'); // Your application ID, see https://developers.facebook.com/docs/apps/register
define('FB_APP_SECRET', 'CHANGE THIS');    // What's your Secret key

// Google 
define('G_Client_ID', 'CHANGE THIS'); // Get a Client ID from https://console.developers.google.com/projectselector/apis/library
define('G_Client_Secret', 'CHANGE THIS'); // What's your Secret key
define('G_Redirect_Uri', 'http://ponepaste.org//oauth/google.php'); // Leave this as is
define('G_Application_Name', 'Paste'); // Make sure this matches the name of your application


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

$db_opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, /* throw a fatal exception on database errors */
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, /* Fetch rows as an associative array (hash table) by default */
    PDO::ATTR_EMULATE_PREPARES => false    
];

// Secret key for paste encryption
$sec_key  = "8ac67343e7980b16b31e8311d4377bbb";
define('SECRET',md5($sec_key));

// Set to 1 to enable Apache's mod_rewrite
$mod_rewrite = "1";

// Available GeSHi formats
$geshiformats =array(
   
    'green' => 'Green Text',
    'text' => 'Plain Text',
    'pastedown' => 'pastedown',
    'pastedown_old' => 'pastedown old',
);

// Popular formats that are listed first.
$popular_formats=array(
	'green',
    'text',
	'pastedown',
    'pastedown_old'
);

//Cookie 
?>