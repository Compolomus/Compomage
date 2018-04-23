<?php

namespace Compolomus\Compomage\Tests;

use Compolomus\Compomage\Image;
use PHPUnit\Framework\TestCase;
use Imagick;

class ImageTest extends TestCase
{
    private static $imageGD;
    private static $imageImagick;

    private function getImage($type = 'gd', $new = false)
    {
        if (!self::$imageGD || !self::$imageImagick || $new) {
            self::$imageGD = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::GD);
            self::$imageImagick = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/crop/bee.jpg', Image::IMAGICK);
        }

        return $type == 'gd' ? self::$imageGD : self::$imageImagick;
    }

    public function test__constructGD()
    {
        try {
            $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::GD);
            $this->assertInternalType('object', $obj);
            $this->assertInstanceOf(Image::class, $obj);
        } catch (\Exception $e) {
            $this->assertContains('Must be initialized ', $e->getMessage());
        }
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $base64_image = base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg'));
        $obj = new Image($base64_image, Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $URL_image = 'https://4.bp.blogspot.com/-P_yzboTrLUM/WGP4FUvVAQI/AAAAAAAABGc/SkRu_mOPKOwxsxLic-dBhugEyvPgvLEqgCLcB/s320/1.png';
        $obj = new Image($URL_image, Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');
    }

    public function test__constructImagick()
    {
        try {
            $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::IMAGICK);
            $this->assertInternalType('object', $obj);
            $this->assertInstanceOf(Image::class, $obj);
            $this->assertInstanceOf(Imagick::class, $obj->getImage());
        } catch (\Exception $e) {
            $this->assertContains('Must be initialized ', $e->getMessage());
        }
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg', Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $base64_image = base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg'));
        $obj = new Image($base64_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $URL_image = 'https://4.bp.blogspot.com/-P_yzboTrLUM/WGP4FUvVAQI/AAAAAAAABGc/SkRu_mOPKOwxsxLic-dBhugEyvPgvLEqgCLcB/s320/1.png';
        $obj = new Image($URL_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function test__constructAuto()
    {
        $obj = new Image(__DIR__ . DIRECTORY_SEPARATOR . '../examples/test.jpg');
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function testResizesBy()
    {
        $this->getImage()->resizeBy('width', 100);
        $this->assertEquals(100, $this->getImage()->getWidth());
        $this->getImage()->resizeBy('height', 150);
        $this->assertEquals(150, $this->getImage()->getHeight());
        $this->getImage()->resizeBy('percent', 50);
        $this->assertEquals(75, $this->getImage()->getHeight());
    }

    public function testThumbnailGD()
    {
        $this->getImage()->thumbnail(100, 100);
        $this->assertEquals(100, $this->getImage()->getHeight());
        $this->assertEquals(100, $this->getImage()->getWidth());
    }

    public function testThumbnailImagick()
    {
        $this->getImage('Imagick')->thumbnail(100, 100);
        $this->assertEquals(100, $this->getImage()->getHeight());
        $this->assertEquals(100, $this->getImage()->getWidth());
    }

    public function testGetFontsList()
    {
        $this->assertInternalType('array', $this->getImage('Imagick')->getFontsList());
    }

    public function testGetBase64()
    {
        $gdBase64 = $this->getImage('gd')->getBase64();
        $obj = new Image($gdBase64, Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $imagickBase64 = $this->getImage('imagick')->getBase64();
        $obj = new Image($imagickBase64, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function testResize()
    {
        $obj = $this->getImage('gd', true);
        $obj->resize(170, 150);
        $this->assertEquals(170, $obj->getWidth());
        $this->assertEquals(150, $obj->getHeight());

        $obj = $this->getImage('imagick', true);
        $obj->resize(160, 140);
        $this->assertEquals(160, $obj->getWidth());
        $this->assertEquals(140, $obj->getHeight());
    }

    public function testRotate()
    {
        $obj = $this->getImage('gd', true);
        $obj->rotate();
        $this->assertEquals(609, $obj->getWidth());

        $obj = $this->getImage('imagick', true);
        $obj->rotate();
        $this->assertEquals(300, $obj->getWidth());
    }

    public function testSave()
    {
        $gdFile = __DIR__ . DIRECTORY_SEPARATOR . './gdTest';
        $imagickFile = __DIR__ . DIRECTORY_SEPARATOR . './imagickTest';

        $obj = $this->getImage('gd');
        $obj->save($gdFile);
        $this->assertFileExists($gdFile . '.png');

        $obj = $this->getImage('imagick');
        $obj->save($imagickFile);
        $this->assertFileExists($imagickFile . '.png');

        unlink($gdFile . '.png');
        unlink($imagickFile . '.png');
    }

    public function testGrayscale()
    {
        $obj = $this->getImage('imagick');
        $obj->grayscale();
        $this->assertEquals($obj->getImage()->getImageColorspace(), \imagick::COLORSPACE_GRAY);
    }
}
