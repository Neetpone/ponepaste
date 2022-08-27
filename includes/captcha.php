<?php
function setupCaptcha() : string {
    global $redis;
    $allowed = "ABCDEFGHIJKLMNOPQRSTUVYXYZabcdefghijklmnopqrstuvwxyz0123456789";

    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= substr($allowed, rand() % (strlen($allowed)), 1);
    }

    $token = pp_random_password();

    $redis->setex('captcha/' . md5($token), 600, $code);

    return $token;
}

function checkCaptcha(string $token, string $answer) : bool {
    global $redis;

    $redis_answer = $redis->get('captcha/' . md5($token));
    if (!$redis_answer) {
        return false;
    }

    $redis->del('captcha/' . $token);

    return strtolower($redis_answer) === strtolower($answer);
}
