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
        $this->imageGD = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::GD);
        $this->imageImagick = new Image(dirname(__FILE__, 2) . '/examples/crop/bee.jpg', Image::IMAGICK);
    }

    /**
     * @throws Exception
     */
    public function test__constructGD(): void
    {
        try {
            $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::GD);
            $this->assertIsObject($obj);
            $this->assertInstanceOf(Image::class, $obj);
        } catch (Exception $e) {
            $this->assertStringContainsString('Image create failed ', $e->getMessage());
        }

        $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::GD);

        $base64_image = base64_encode(file_get_contents(dirname(__FILE__, 2) . '/examples/test.jpg'));
        $obj = new Image($base64_image, Image::GD);

        $URL_image = 'https://help.ubuntu.ru/_media/wiki/tux_150x150.png';
        $obj = new Image($URL_image, Image::GD);
    }

    public function test__constructImagick(): void
    {
        try {
            $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::IMAGICK);
            $this->assertIsObject($obj);
            $this->assertInstanceOf(Image::class, $obj);
            $this->assertInstanceOf(Imagick::class, $obj->getImage());
        } catch (Exception $e) {
            $this->assertStringContainsString('Must be initialized ', $e->getMessage());
        }
        $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $base64_image = base64_encode(file_get_contents(dirname(__FILE__, 2) . '/examples/test.jpg'));
        $obj = new Image($base64_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $URL_image = 'https://help.ubuntu.ru/_media/wiki/tux_150x150.png';
        $obj = new Image($URL_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function test__constructAuto(): void
    {
        $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg');
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

    public function testGetBase64(): void
    {
        $gdBase64 = $this->imageGD->getBase64();
        $obj = new Image($gdBase64, Image::GD);

        $imagickBase64 = $this->imageImagick->getBase64();
        $obj = new Image($imagickBase64, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
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
        $gdFile = dirname(__FILE__, 2) . '/gdTest';
        $imagickFile = dirname(__FILE__, 2) . '/imagickTest';

        mkdir($gdFile, 0777, true);
        mkdir($imagickFile, 0777, true);

        $this->imageGD->save($gdFile);
        $this->assertFileExists($gdFile . '.png');

        $this->imageImagick->save($imagickFile);
        $this->assertFileExists($imagickFile . '.png');

        unlink($gdFile . '.png');
        unlink($imagickFile . '.png');

        rmdir($gdFile);
        rmdir($imagickFile);
    }

//    public function testGrayscale()
//    {
//        $obj = $this->getImage('imagick');
//        $obj->grayscale();
//#        echo $obj->getImage()->getImageColorspace(), '<br>', \imagick::COLORSPACE_GRAY;
//        $this->assertEquals($obj->getImage()->getImageColorspace(), \imagick::COLORSPACE_GRAY);
//    }
}
