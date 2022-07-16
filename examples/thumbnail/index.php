<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

// test GD

(new Image('../crop/bee.jpg', Image::GD))
    ->copyright('Test', realpath('../arial.ttf'), 'CENTER')
    ->thumbnail(170, 180)
    ->save('test1');

$URL_image = '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';

$img = new Image($URL_image, Image::GD);
    $img->grayscale()
    ->thumbnail(170, 180)
    ->save('./test2');

$base64_image = base64_encode(file_get_contents('../test.jpg'));

$img = new Image($base64_image, Image::GD);
$img->thumbnail(200, 100);
echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';
echo '<pre>' . print_r($img, true) . '</pre>';
$img->save('./thumbnail_test1_gd');

$base64_image1 = base64_encode(file_get_contents('../crop/bee.jpg'));

$img = new Image($base64_image1, Image::GD);
$img->thumbnail(170, 180);
echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';
echo '<pre>' . print_r($img, true) . '</pre>';
$img->save('./thumbnail_test2_gd');

if (extension_loaded('imagick')) {

// test Imagick

    $img = new Image($base64_image, Image::IMAGICK);
    $font = $img->getFontsList()[0];
    $img->thumbnail(200, 100);
    echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

    $img->save('./thumbnail_test1_im');

    $img = new Image(base64_encode(file_get_contents('../test.jpg')), Image::IMAGICK);
    $img->copyright('Test', $font, 'CENTER')
        ->thumbnail(170, 180)
        ->save('./test3');

    $img = new Image($base64_image1, Image::IMAGICK);
    $img->thumbnail(170, 180);
    echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';
    echo '<pre>' . print_r($img, true) . '</pre>';
    // save Imagick test
    $img->save('./thumbnail_test2_im');
} else {
    echo 'Imagick not supported';
}

//$files = [
//    'test1.png',
//    'test2.png',
//    'test3.png',
//    'thumbnail_test1_gd.png',
//    'thumbnail_test1_im.png',
//    'thumbnail_test2_gd.png',
//    'thumbnail_test2_im.png',
//];
//
//foreach ($files as $file) {
//    @unlink($file);
//}
