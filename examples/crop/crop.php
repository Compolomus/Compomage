<?php

use Compolomus\Compomage\Image;

require '../../vendor/autoload.php';

/**
 * @param array $args
 * @return string
 * @throws Exception
 */
function out_base64(array $args): string
{
    $obj = new Image($args['image'], Image::IMAGICK); // Image::IMAGICK

    return '<img src="data:image/png;base64,' . $obj->crop(
            (int) $args['width'],
            (int) $args['height'],
            (int) $args['x'],
            (int) $args['y']
        )
            ->getBase64() . '" alt="base64_image" />'
        . '<div>input w h x y = ' . implode(' | ', [$args['width'], $args['height'], $args['x'], $args['y']]
        ) . '</div>';
}

echo out_base64($_REQUEST);
