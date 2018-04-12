<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

// test GD

$base64_image = base64_encode(file_get_contents('../test.jpg'));

$img = new Image($base64_image, Image::GD);

$img->thumbnail(200, 100);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

echo '<pre>' . print_r($img, true) . '</pre>';

$img->save('thumbnail_test1_gd');

$base64_image1 = base64_encode(file_get_contents('../crop/bee.jpg'));

$img = new Image($base64_image1, Image::GD);

$img->thumbnail(170, 180);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

echo '<pre>' . print_r($img, true) . '</pre>';

$img->save('thumbnail_test2_gd');

// test Imagick

$img = new Image($base64_image, Image::IMAGICK);

$img->thumbnail(200, 100);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

echo '<pre>' . print_r($img, true) . '</pre>';

$img->save('thumbnail_test1_im');

$img = new Image($base64_image1, Image::IMAGICK);

$img->thumbnail(170, 180);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

echo '<pre>' . print_r($img, true) . '</pre>';

// save Imagick test

$img->save('thumbnail_test2_im');
