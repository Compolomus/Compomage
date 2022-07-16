<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

// test GD

$base64_image = base64_encode(file_get_contents('../test.jpg'));

$img = new Image($base64_image, Image::GD);
$watermark = new Image('ImageMagick.png', Image::GD);
echo '<img src="data:image/png;base64,' . $watermark->resizeByHeight(100)->getBase64() . '" alt="base64_image" style="background-color: white;" />';
$watermark->resizeByHeight(100);
$img->watermark($watermark, 'CENTER');
$img->copyright('GD test', realpath('../couri.ttf'), 'SOUTHEAST');
echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

if (extension_loaded('imagick')) {
// test Imagick

	$img = new Image($base64_image, Image::IMAGICK);
	$watermark = new Image('ImageMagick.png', Image::IMAGICK);
	$watermark->resizeByHeight(100);
	$img->watermark($watermark, 'CENTER');
    $font = $img->getFontsList()[0];
	$img->copyright('Imagick test', $font, 'SOUTHEAST');
	echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';
} else {
	echo 'Imagick not supported';
}
