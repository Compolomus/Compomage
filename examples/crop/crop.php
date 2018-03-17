<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

/**
 * @param string $image
 * @param int $width
 * @param int $height
 * @param int $startX
 * @param int $startY
 * @return string
 * @throws Exception
 */
function out_base64(string $image, int $width, int $height, int $startX, int $startY): string
{
    $obj = new Image($image, Image::GD); // Image::IMAGICK
    return '<img src="data:image/png;base64,' . $obj->crop( $width, $height, $startX, $startY)->getBase64() . '" alt="base64_image" />'
        . '<div>input w h x y = ' . implode(' | ',  [$width, $height, $startX, $startY]) . '</div>';
}

echo out_base64($_REQUEST['image'], $_REQUEST['width'], $_REQUEST['height'], $_REQUEST['x'], $_REQUEST['y']);
