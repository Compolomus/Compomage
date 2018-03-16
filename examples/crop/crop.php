<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

/**
 * @param ImageInterface $image
 * @param $w
 * @param $h
 * @param $x
 * @param $y
 * @return string
 * @throws Exception
 */
function out_base64($image, $w, $h, $x, $y)
{
    $obj = new Image($image);
    return '<img src="data:image/png;base64,' . $obj->crop($w, $h, $x, $y)->getBase64() . '" alt="base64_image" />';
}
//echo '<pre>' . print_r($_REQUEST, 1) . '</pre>';
echo out_base64($_REQUEST['image'], $_REQUEST['width'], $_REQUEST['height'], $_REQUEST['x'], $_REQUEST['y']);
