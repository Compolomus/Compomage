# Koenig Compomage


[![License](https://poser.pugx.org/compolomus/Compomage/license)](https://packagist.org/packages/compolomus/Compomage)

[![Build Status](https://scrutinizer-ci.com/g/Compolomus/Compomage/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Compolomus/Compomage/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Compolomus/Compomage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Compolomus/Compomage/?branch=master)
[![Code Climate](https://codeclimate.com/github/Compolomus/Compomage/badges/gpa.svg)](https://codeclimate.com/github/Compolomus/Compomage)
[![Downloads](https://poser.pugx.org/compolomus/Compomage/downloads)](https://packagist.org/packages/compolomus/Compomage)

## Установка:

composer require compolomus/compomage

## Применение:

```php

use Compolomus\Compomage\Image;

require __DIR__ . '/vendor/autoload.php';


/* Local file */
$img = new Image('./examples/crop/bee.jpg'); // Auto check Imagick or GD default

$img->rotate(45)
    ->grayscale()
    ->flop();

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" style="background-color: orange;" />';

/* base64 source */

$base64_image = base64_encode(file_get_contents('./examples/crop/bee.jpg'));

$img = new Image($base64_image, Image::GD);

$img->resizeBy('percent', 150)
    ->flip();

echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" />';

/* URL file */

$URL_image = 'https://4.bp.blogspot.com/-P_yzboTrLUM/WGP4FUvVAQI/AAAAAAAABGc/SkRu_mOPKOwxsxLic-dBhugEyvPgvLEqgCLcB/s320/1.png';

$img = new Image($URL_image, Image::IMAGICK);

$img->resizeByWidth(600)
    ->resizeByHeight(350);
    
echo '<img src="data:image/png;base64,' . $img->getBase64() . '" alt="base64_image" />';

```
