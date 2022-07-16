<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

// transparent

$base64_image = base64_encode(file_get_contents('../test.jpg'));

$img = new Image($base64_image, Image::GD);
#echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" /><hr>';
$img->resizeByTransparentBackground(600, 700);
echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" /><hr>';

$img = new Image($base64_image);
$img->resizeByTransparentBackground(600, 700);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" /><hr>';

$base64_image = base64_encode(file_get_contents('./../crop/bee.jpg'));

$img = new Image($base64_image);
$img->resizeByTransparentBackground(600, 700);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

// blur

$img = new Image($base64_image);
$img->resizeByBlurBackground(700, 600);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" /><hr>';

$base64_image = base64_encode(file_get_contents('../test.jpg'));

$img = new Image($base64_image);
$img->resizeByBlurBackground(600, 700);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

$img = new Image($base64_image);
$img->resizeByBlurBackground(700, 600);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" /><hr>';

$base64_image = base64_encode(file_get_contents('./../crop/bee.jpg'));

$img = new Image($base64_image);
$img->resizeByBlurBackground(600, 700);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

$img = new Image($base64_image);
$img->resizeByTransparentBackground(700, 600);

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';
