<?php

namespace Compolomus\Compomage\Tests;

use Compolomus\Compomage\Image;
use Exception;
use PHPUnit\Framework\TestCase;
use Imagick;

class ImageTest extends TestCase
{
    private static $imageGD;
    private static $imageImagick;

    protected function setUp(): void
    {
        self::$imageGD = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::GD);
        self::$imageImagick = new Image(dirname(__FILE__, 2) . '/examples/crop/bee.jpg', Image::IMAGICK);
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
        self::$imageGD->resizeBy('width', 100);
        $this->assertEquals(100, self::$imageGD->getWidth());
        self::$imageGD->resizeBy('height', 150);
        $this->assertEquals(150, self::$imageGD->getHeight());
        self::$imageGD->resizeBy('percent', 50);
        $this->assertEquals(75, self::$imageGD->getHeight());
    }

    public function testThumbnailGD(): void
    {
        self::$imageGD->thumbnail(100, 100);
        $this->assertEquals(100, self::$imageGD->getHeight());
        $this->assertEquals(100, self::$imageGD->getWidth());
    }

    public function testThumbnailImagick(): void
    {
        self::$imageImagick->thumbnail(100, 100);
        $this->assertEquals(100, self::$imageImagick->getHeight());
        $this->assertEquals(100, self::$imageImagick->getWidth());
    }

    public function testGetFontsList(): void
    {
        $this->assertIsArray(self::$imageImagick->getFontsList());
    }

    public function testGetBase64(): void
    {
        $gdBase64 = self::$imageGD->getBase64();
        $obj = new Image($gdBase64, Image::GD);

        $imagickBase64 = self::$imageImagick->getBase64();
        $obj = new Image($imagickBase64, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function testResize(): void
    {
        self::$imageGD->resize(170, 150);
        $this->assertEquals(170, self::$imageGD->getWidth());
        $this->assertEquals(150, self::$imageGD->getHeight());

        self::$imageImagick->resize(160, 140);
        $this->assertEquals(160, self::$imageImagick->getWidth());
        $this->assertEquals(140, self::$imageImagick->getHeight());
    }

    public function testRotate(): void
    {
        self::$imageGD->rotate();
        $this->assertEquals(609, self::$imageGD->getWidth());

        self::$imageImagick->rotate();
        $this->assertEquals(300, self::$imageImagick->getWidth());
    }

    public function testSave(): void
    {
        $gdFile = dirname(__FILE__, 2) . '/gdTest';
        $imagickFile = dirname(__FILE__, 2) . '/imagickTest';

        mkdir($gdFile, 0777, true);
        mkdir($imagickFile, 0777, true);

        self::$imageGD->save($gdFile);
        $this->assertFileExists($gdFile . '.png');

        self::$imageImagick->save($imagickFile);
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
