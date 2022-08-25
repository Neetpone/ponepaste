<?php

use JetBrains\PhpStorm\ArrayShape;

#[ArrayShape(['code' => "mixed|string", 'image_src' => "string"])]
function captcha($color, $mul, $allowed) : array {
    $bg_path = __DIR__ . '/../public/assets/img/captcha/';
    $font_path = __DIR__ . '/../public/assets/fonts/';
    $fonts = [
        $font_path . 'LMS Pretty Pony.ttf',
        $font_path . 'PonyvilleMedium0.4.ttf'
    ];

    $backgrounds = [
        $bg_path . 'text3.png',
        $bg_path . 'text2.png',
        $bg_path . 'text1.png'
    ];

    $captcha_config = [
        'min_length' => 5,
        'max_length' => 5,
        'backgrounds' => $backgrounds,
        'fonts' => $fonts,
        'characters' => $allowed,
        'min_font_size' => 28,
        'max_font_size' => 28,
        'color' => $color,
        'angle_min' => 0,
        'angle_max' => 10,
        'shadow' => true,
        'shadow_color' => '#fff',
        'shadow_offset_x' => -1,
        'shadow_offset_y' => 1
    ];

    // Overwrite defaults with custom config values
    if (!empty($config) && is_array($config)) {
        foreach ($config as $key => $value) {
            $captcha_config[$key] = $value;
        }
    }

    // Restrict certain values
    if ($captcha_config['min_length'] < 1) {
        $captcha_config['min_length'] = 1;
    }

    if ($captcha_config['angle_min'] < 0) {
        $captcha_config['angle_min'] = 0;
    }

    if ($captcha_config['angle_max'] > 10) {
        $captcha_config['angle_max'] = 10;
    }

    if ($captcha_config['angle_max'] < $captcha_config['angle_min']) {
        $captcha_config['angle_max'] = $captcha_config['angle_min'];
    }

    if ($captcha_config['min_font_size'] < 10) {
        $captcha_config['min_font_size'] = 10;
    }

    if ($captcha_config['max_font_size'] < $captcha_config['min_font_size']) {
        $captcha_config['max_font_size'] = $captcha_config['min_font_size'];
    }

    $captcha_config['code'] = '';


    return $captcha_config;
}

if (!function_exists('hex2rgb')) {
    function hex2rgb($hex_str) : array|null {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string

        if (strlen($hex_str) == 6) {
            $color_val = hexdec($hex_str);
            return [
                'r' => 0xFF & ($color_val >> 0x10),
                'g' => 0xFF & ($color_val >> 0x8),
                'b' => 0xFF & $color_val
            ];
        } elseif (strlen($hex_str) == 3) {
            return [
                'r' => hexdec(str_repeat(substr($hex_str, 0, 1), 2)),
                'g' => hexdec(str_repeat(substr($hex_str, 1, 1), 2)),
                'b' => hexdec(str_repeat(substr($hex_str, 2, 1), 2))
            ];
        }

        return null;
    }
}

function setupCaptcha() : string {
    global $captcha_config;
    global $redis;

    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= substr($captcha_config['allowed'], rand() % (strlen($captcha_config['allowed'])), 1);
    }

    $token = pp_random_password();

    $redis->setex('captcha/' . md5($token), 600, $code);

    return $token;
}

function checkCaptcha(string $token, string $answer) : bool {
    global $redis;

    $redis_answer = $redis->get('captcha/' . $token);
    if (!$redis_answer) {
        return false;
    }

    $redis->del('captcha/' . $token);

    return $redis_answer === $answer;
}
