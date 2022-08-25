<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');
require_once(__DIR__ . '/../includes/captcha.php');

if (empty($_GET['t'])) {
    die('No token provided.');
}

$captcha_token = 'captcha/' . md5($_GET['t']);
$captcha_code = $redis->get($captcha_token);

if (!$captcha_code) {
    die('No token provided.');
}

$captcha_config = captcha($captcha_config['colour'], $captcha_config['multiple'], $captcha_config['allowed']);

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

// Draw shadow
if ($captcha_config['shadow']) {
    $shadow_color = hex2rgb($captcha_config['shadow_color']);
    $shadow_color = imagecolorallocate($captcha, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
    imagettftext($captcha, $font_size, $angle, $text_pos_x + $captcha_config['shadow_offset_x'], $text_pos_y + $captcha_config['shadow_offset_y'], $shadow_color, $font, $captcha_code);
}

// Draw text
imagettftext($captcha, $font_size, $angle, $text_pos_x, $text_pos_y, $color, $font, $captcha_code);

// Output image
header("Content-type: image/png");
imagepng($captcha);
