<?php
require_once 'includes/passwords.php';

// https://stackoverflow.com/a/1674175
function prompt_silent($prompt = "Enter Password:") {
    if (preg_match('/^win/i', PHP_OS)) {
        $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
        file_put_contents(
            $vbscript, 'wscript.echo(InputBox("'
            . addslashes($prompt)
            . '", "", "password here"))');
        $command = "cscript //nologo " . escapeshellarg($vbscript);
        $password = rtrim(shell_exec($command));
        unlink($vbscript);
        return $password;
    } else {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");
            return null;
        }
        $command = "/usr/bin/env bash -c 'read -s -p \""
            . addslashes($prompt)
            . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        echo "\n";
        return $password;
    }
}

if (count($argv) != 2) {
    die("usage: {$argv[0]} <hashpw/checkpw/gentoken>");
}

if ($argv[1] === 'hashpw') {
    $password = prompt_silent('Password to hash (will not be echoed): ');

    if (empty($password)) {
        die('Error: no password entered, or password prompting failed.');
    }

    echo pp_password_hash($password);
} elseif ($argv[1] === 'checkpw') {
    $password = prompt_silent('Password to check (will not be echoed): ');
    $hash = prompt_silent('Hash to check against (will not be echoed): ');

    if (empty($password) || empty($hash)) {
        die('Error: no password or no hash entered, or prompting failed.');
    }

    echo pp_password_verify($password, $hash) ? 'ok' : 'fail';
} else if ($argv[1] === 'gentoken') {
    echo pp_random_friendly_token();
}
?>

