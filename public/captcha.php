<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');
require_once(__DIR__ . '/../includes/captcha.php');

if (empty($_GET['t'])) {
    die('Invalid token provided.');
}

$captcha_token = 'captcha/' . md5($_GET['t']);
$captcha_code = $redis->get($captcha_token);

if (!$captcha_code) {
    die('Invalid token provided.');
}

$bg_path = __DIR__ . '/assets/img/captcha/';
$font_path = __DIR__ . '/assets/fonts/';

$captcha_config = [
    'min_length' => 5,
    'max_length' => 5,
    'backgrounds' => [
        $bg_path . 'text3.png',
        $bg_path . 'text2.png',
        $bg_path . 'text1.png'
    ],
    'fonts' => [
        $font_path . 'LMS Pretty Pony.ttf',
        $font_path . 'PonyvilleMedium0.4.ttf'
    ],
    'min_font_size' => 28,
    'max_font_size' => 28,
    'color' => ['r' => 0, 'g' => 0, 'b' => 0],
    'angle_min' => 0,
    'angle_max' => 10,
    'shadow' => true,
    'shadow_color' => ['r' => 0xFF, 'g' => 0xFF, 'b' => 0xFF],
    'shadow_offset_x' => -1,
    'shadow_offset_y' => 1
];

// Pick random background, get info, and start captcha
$background = $captcha_config['backgrounds'][rand(0, count($captcha_config['backgrounds']) - 1)];
list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($background);

$captcha = imagecreatefrompng($background);

$color = $captcha_config['color'];
$color = imagecolorallocate($captcha, $color['r'], $color['g'], $color['b']);

// Determine text angle
$angle = rand($captcha_config['angle_min'], $captcha_config['angle_max']) * (rand(0, 1) == 1 ? -1 : 1);

// Select font randomly
$font = $captcha_config['fonts'][rand(0, count($captcha_config['fonts']) - 1)];

// Verify font file exists
if (!file_exists($font)) {
    die('Font file not found.');
}

// Set the font size
$font_size = rand($captcha_config['min_font_size'], $captcha_config['max_font_size']);
$text_box_size = imagettfbbox($font_size, $angle, $font, $captcha_code);

// Determine text position
$box_width = (int) abs($text_box_size[6] - $text_box_size[2]);
$box_height = (int) abs($text_box_size[5] - $text_box_size[1]);
$text_pos_x_min = 0;
$text_pos_x_max = (int) ($bg_width - $box_width);
$text_pos_x = rand($text_pos_x_min, $text_pos_x_max);
$text_pos_y_min = $box_height;
$text_pos_y_max = (int) ($bg_height - ($box_height / 2));
$text_pos_y = rand($text_pos_y_min, $text_pos_y_max);

// Draw text
imagettftext($captcha, $font_size, $angle, $text_pos_x, $text_pos_y, $color, $font, $captcha_code);

// Output image
header("Content-type: image/png");
imagepng($captcha);
