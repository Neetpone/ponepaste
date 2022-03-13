<?php

use JetBrains\PhpStorm\ArrayShape;

#[ArrayShape(['code' => "mixed|string", 'image_src' => "string"])]
function captcha($color, $mode, $mul, $allowed) : array {

    $bg_path = __DIR__ . '/../assets/img/captcha/';
    $font_path = __DIR__ . '/../assets/fonts/';

    if ($mul == "on") {
        $captcha_config = array(
            'code' => '',
            'min_length' => 5,
            'max_length' => 6,
            'backgrounds' => array(
                $bg_path . 'text3.png',
                $bg_path . 'text2.png',
                $bg_path . 'text1.png'
            ),
            'fonts' => array(
                $font_path . 'LMS Pretty Pony.ttf',
                $font_path . 'PonyvilleMedium0.4.ttf',
                $font_path . 'PonyvilleMedium0.4.ttf'
            ),
            'characters' => $allowed,
            'min_font_size' => 20,
            'max_font_size' => 28,
            'color' => $color,
            'angle_min' => 0,
            'angle_max' => 5,
            'shadow' => true,
            'shadow_color' => '#fff',
            'shadow_offset_x' => -2,
            'shadow_offset_y' => 4
        );
    } else {
        $captcha_config = array(
            'code' => '',
            'min_length' => 5,
            'max_length' => 5,
            'backgrounds' => array(
                $bg_path . 'text2.png'
            ),
            'fonts' => array(
                $font_path . 'times_new_yorker.ttf'
            ),
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
        );
    }

    // Overwrite defaults with custom config values
    if (!empty($config) && is_array($config)) {
        foreach ($config as $key => $value)
            $captcha_config[$key] = $value;
    }

    // Restrict certain values
    if ($captcha_config['min_length'] < 1)
        $captcha_config['min_length'] = 1;
    if ($captcha_config['angle_min'] < 0)
        $captcha_config['angle_min'] = 0;
    if ($captcha_config['angle_max'] > 10)
        $captcha_config['angle_max'] = 10;
    if ($captcha_config['angle_max'] < $captcha_config['angle_min'])
        $captcha_config['angle_max'] = $captcha_config['angle_min'];
    if ($captcha_config['min_font_size'] < 10)
        $captcha_config['min_font_size'] = 10;
    if ($captcha_config['max_font_size'] < $captcha_config['min_font_size'])
        $captcha_config['max_font_size'] = $captcha_config['min_font_size'];


    // Generate CAPTCHA code if not set by user
    if (empty($captcha_config['code'])) {
        $captcha_config['code'] = '';
        $length = rand($captcha_config['min_length'], $captcha_config['max_length']);
        while (strlen($captcha_config['code']) < $length) {
            $captcha_config['code'] .= substr($captcha_config['characters'], rand() % (strlen($captcha_config['characters'])), 1);
        }
    }

    // Generate HTML for image src
    $image_src = substr(__FILE__, strlen(realpath($_SERVER['DOCUMENT_ROOT']))) . '?_CAPTCHA&amp;t=' . urlencode(microtime());
    $image_src = '/' . ltrim(preg_replace('/\\\\/', '/', $image_src), '/');

    $_SESSION['_CAPTCHA']['config'] = serialize($captcha_config);

    return [
        'code' => $captcha_config['code'],
        'image_src' => $image_src
    ];
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

// Draw the image
if (isset($_GET['_CAPTCHA'])) {

    session_start();

    $captcha_config = unserialize(@$_SESSION['_CAPTCHA']['config']);
    if (!$captcha_config)
        exit();

    // Pick random background, get info, and start captcha
    $background = $captcha_config['backgrounds'][rand(0, count($captcha_config['backgrounds']) - 1)];
    list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($background);

    $captcha = imagecreatefrompng($background);

    $color = hex2rgb($captcha_config['color']);
    $color = imagecolorallocate($captcha, $color['r'], $color['g'], $color['b']);

    // Determine text angle
    $angle = rand($captcha_config['angle_min'], $captcha_config['angle_max']) * (rand(0, 1) == 1 ? -1 : 1);

    // Select font randomly
    $font = $captcha_config['fonts'][rand(0, count($captcha_config['fonts']) - 1)];

    // Verify font file exists
    if (!file_exists($font))
        throw new Exception('Font file not found: ' . $font);

    // Set the font size
    $font_size = rand($captcha_config['min_font_size'], $captcha_config['max_font_size']);
    $text_box_size = imagettfbbox($font_size, $angle, $font, $captcha_config['code']);

    // Determine text position
    $box_width = abs($text_box_size[6] - $text_box_size[2]);
    $box_height = abs($text_box_size[5] - $text_box_size[1]);
    $text_pos_x_min = 0;
    $text_pos_x_max = ($bg_width) - ($box_width);
    $text_pos_x = rand($text_pos_x_min, $text_pos_x_max);
    $text_pos_y_min = $box_height;
    $text_pos_y_max = ($bg_height) - ($box_height / 2);
    $text_pos_y = rand($text_pos_y_min, $text_pos_y_max);

    // Draw shadow
    if ($captcha_config['shadow']) {
        $shadow_color = hex2rgb($captcha_config['shadow_color']);
        $shadow_color = imagecolorallocate($captcha, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
        imagettftext($captcha, $font_size, $angle, $text_pos_x + $captcha_config['shadow_offset_x'], $text_pos_y + $captcha_config['shadow_offset_y'], $shadow_color, $font, $captcha_config['code']);
    }

    // Draw text
    imagettftext($captcha, $font_size, $angle, $text_pos_x, $text_pos_y, $color, $font, $captcha_config['code']);

    // Output image
    header("Content-type: image/png");
    imagepng($captcha);

}