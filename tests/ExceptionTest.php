<?php

namespace Compolomus\Compomage\Tests;

use Compolomus\Compomage\Image;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ExceptionTest extends TestCase
{
    public function testGetImageByURL(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $URL_image = 'https://test.ru';
        new Image($URL_image, Image::GD);
    }

    public function testResizeBy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::GD);
        $obj->resizeBy('test', 111);
    }

    public function testTmp(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $base64_image = base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../examples/crop/index.php'));
        new Image($base64_image, Image::GD);
    }

    public function testCopyrightGD(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::GD);
        $obj->copyright('test', realpath(__DIR__ . DIRECTORY_SEPARATOR . '../examples/arial.ttf'), 'test');
    }

    public function testCopyrightImagick(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::IMAGICK);
        $obj->copyright('test', 'test', 'test');
    }

    public function testWatermark(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::IMAGICK);
        $watermark = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/watermark/ImageMagick.png', Image::IMAGICK);
        $obj->watermark($watermark, 'test');
    }

    public function test__call(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::IMAGICK);
        $obj->test('test', 'test', 123);
    }
}
