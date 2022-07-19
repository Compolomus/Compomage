<?php

namespace Compolomus\Compomage\Tests;

use Compolomus\Compomage\Image;
use Exception;
use PHPUnit\Framework\TestCase;
use Imagick;

class ImageTest extends TestCase
{
    public Image $imageGD;
    public Image $imageImagick;

    public function setUp(): void
    {
        $this->imageGD = new Image(__DIR__ . '/test.jpg', Image::GD);
        $this->imageImagick = new Image(__DIR__ . '/bee.jpg', Image::IMAGICK);
    }

    /**
     * @throws Exception
     */
    public function testConstructGD(): void
    {
        $this->assertIsObject($this->imageGD);
        $this->assertInstanceOf(Image::class, $this->imageGD);

        $base64_image = base64_encode(file_get_contents(__DIR__ . '/test.jpg'));
        $obj = new Image($base64_image, Image::GD);
        $this->assertInstanceOf(Image::class, $obj);

        $URL_image = 'https://help.ubuntu.ru/_media/wiki/tux_150x150.png';
        $obj = new Image($URL_image, Image::GD);
        $this->assertInstanceOf(Image::class, $obj);
    }

    public function testConstructImagick(): void
    {
        $this->assertIsObject($this->imageImagick);
        $this->assertInstanceOf(Image::class, $this->imageImagick);
        $this->assertInstanceOf(Imagick::class, $this->imageImagick->getImage());

        $base64_image = base64_encode(file_get_contents(__DIR__ . '/test.jpg'));
        $obj = new Image($base64_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $URL_image = 'https://help.ubuntu.ru/_media/wiki/tux_150x150.png';
        $obj = new Image($URL_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function testConstructAuto(): void
    {
        $obj = new Image(__DIR__ . '/test.jpg');
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function testResizesBy(): void
    {
        $this->imageGD->resizeBy('width', 100);
        $this->assertEquals(100, $this->imageGD->getWidth());
        $this->imageGD->resizeBy('height', 150);
        $this->assertEquals(150, $this->imageGD->getHeight());
        $this->imageGD->resizeBy('percent', 50);
        $this->assertEquals(75, $this->imageGD->getHeight());
    }

    public function testThumbnailGD(): void
    {
        $this->imageGD->thumbnail(100, 100);
        $this->assertEquals(100, $this->imageGD->getHeight());
        $this->assertEquals(100, $this->imageGD->getWidth());
    }

    public function testThumbnailImagick(): void
    {
        $this->imageImagick->thumbnail(100, 100);
        $this->assertEquals(100, $this->imageImagick->getHeight());
        $this->assertEquals(100, $this->imageImagick->getWidth());
    }

    public function testGetFontsList(): void
    {
        $this->assertIsArray($this->imageImagick->getFontsList());
    }

    public function testResize(): void
    {
        $this->imageGD->resize(170, 150);
        $this->assertEquals(170, $this->imageGD->getWidth());
        $this->assertEquals(150, $this->imageGD->getHeight());

        $this->imageImagick->resize(160, 140);
        $this->assertEquals(160, $this->imageImagick->getWidth());
        $this->assertEquals(140, $this->imageImagick->getHeight());
    }

    public function testRotate(): void
    {
        $this->imageGD->rotate();
        $this->assertEquals(609, $this->imageGD->getWidth());

        $this->imageImagick->rotate();
        $this->assertEquals(300, $this->imageImagick->getWidth());
    }

    public function testSave(): void
    {
        $gdFile = __DIR__ . '/gdTest';
        $imagickFile = __DIR__ . '/imagickTest';

        $this->imageGD->save($gdFile);
        $this->assertFileExists($gdFile . '.png');

        $this->imageImagick->save($imagickFile);
        $this->assertFileExists($imagickFile . '.png');

        unlink($gdFile . '.png');
        unlink($imagickFile . '.png');
    }

//    public function testGrayscale()
//    {
//        $obj = $this->getImage('imagick');
//        $obj->grayscale();
//#        echo $obj->getImage()->getImageColorspace(), '<br>', \imagick::COLORSPACE_GRAY;
//        $this->assertEquals($obj->getImage()->getImageColorspace(), \imagick::COLORSPACE_GRAY);
//    }
}
