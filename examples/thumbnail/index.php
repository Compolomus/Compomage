<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

// test GD

(new Image('../crop/bee.jpg', Image::GD))
    ->copyright('Test', realpath('../arial.ttf'), 'CENTER')
    ->thumbnail(170, 180)
    ->save('test1');

$URL_image = 'https://4.bp.blogspot.com/-P_yzboTrLUM/WGP4FUvVAQI/AAAAAAAABGc/SkRu_mOPKOwxsxLic-dBhugEyvPgvLEqgCLcB/s320/1.png';

(new Image($URL_image))
    ->grayscale()
    ->thumbnail(170, 180)
    ->save('test3');

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

if (extension_loaded('imagick')) {

// test Imagick

	(new Image(base64_encode(file_get_contents('../test.jpg')), Image::IMAGICK))
		->copyright('Test', 'Courier', 'CENTER')
		->thumbnail(170, 180)
		->save('test2');
		
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
} else {
	echo 'Imagick not supported';
}
