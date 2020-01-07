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

    /**
     * @param string $type
     * @param bool $new
     * @return Image
     */
    private function getImage($type = 'gd', $new = false): Image
    {
        if (!self::$imageGD || !self::$imageImagick || $new) {
            try {
                self::$imageGD = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::GD);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            try {
                self::$imageImagick = new Image(dirname(__FILE__, 2) . '/examples/crop/bee.jpg',
                    Image::IMAGICK);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $type === 'gd' ? self::$imageGD : self::$imageImagick;
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
            $this->assertContains('Must be initialized ', $e->getMessage());
        }
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $base64_image = base64_encode(file_get_contents(dirname(__FILE__, 2) . '/examples/test.jpg'));
        $obj = new Image($base64_image, Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $URL_image = 'https://4.bp.blogspot.com/-P_yzboTrLUM/WGP4FUvVAQI/AAAAAAAABGc/SkRu_mOPKOwxsxLic-dBhugEyvPgvLEqgCLcB/s320/1.png';
        $obj = new Image($URL_image, Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');
    }

    public function test__constructImagick(): void
    {
        try {
            $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::IMAGICK);
            $this->assertIsObject($obj);
            $this->assertInstanceOf(Image::class, $obj);
            $this->assertInstanceOf(Imagick::class, $obj->getImage());
        } catch (Exception $e) {
            $this->assertContains('Must be initialized ', $e->getMessage());
        }
        $obj = new Image(dirname(__FILE__, 2) . '/examples/test.jpg', Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $base64_image = base64_encode(file_get_contents(dirname(__FILE__, 2) . '/examples/test.jpg'));
        $obj = new Image($base64_image, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());

        $URL_image = 'https://4.bp.blogspot.com/-P_yzboTrLUM/WGP4FUvVAQI/AAAAAAAABGc/SkRu_mOPKOwxsxLic-dBhugEyvPgvLEqgCLcB/s320/1.png';
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
        $this->getImage()->resizeBy('width', 100);
        $this->assertEquals(100, $this->getImage()->getWidth());
        $this->getImage()->resizeBy('height', 150);
        $this->assertEquals(150, $this->getImage()->getHeight());
        $this->getImage()->resizeBy('percent', 50);
        $this->assertEquals(75, $this->getImage()->getHeight());
    }

    public function testThumbnailGD(): void
    {
        $this->getImage()->thumbnail(100, 100);
        $this->assertEquals(100, $this->getImage()->getHeight());
        $this->assertEquals(100, $this->getImage()->getWidth());
    }

    public function testThumbnailImagick(): void
    {
        $this->getImage('Imagick')->thumbnail(100, 100);
        $this->assertEquals(100, $this->getImage()->getHeight());
        $this->assertEquals(100, $this->getImage()->getWidth());
    }

    public function testGetFontsList(): void
    {
        $this->assertIsArray($this->getImage('Imagick')->getFontsList());
    }

    public function testGetBase64(): void
    {
        $gdBase64 = $this->getImage('gd')->getBase64();
        $obj = new Image($gdBase64, Image::GD);
        $this->assertEquals(get_resource_type($obj->getImage()), 'gd');

        $imagickBase64 = $this->getImage('imagick')->getBase64();
        $obj = new Image($imagickBase64, Image::IMAGICK);
        $this->assertInstanceOf(Imagick::class, $obj->getImage());
    }

    public function testResize(): void
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

    public function testRotate(): void
    {
        $obj = $this->getImage('gd', true);
        $obj->rotate();
        $this->assertEquals(609, $obj->getWidth());

        $obj = $this->getImage('imagick', true);
        $obj->rotate();
        $this->assertEquals(300, $obj->getWidth());
    }

    public function testSave(): void
    {
        $gdFile =dirname(__FILE__, 2) . '/gdTest';
        $imagickFile = dirname(__FILE__, 2) . '/imagickTest';

        $obj = $this->getImage('gd');
        $obj->save($gdFile);
        $this->assertFileExists($gdFile . '.png');

        $obj = $this->getImage('imagick');
        $obj->save($imagickFile);
        $this->assertFileExists($imagickFile . '.png');

        unlink($gdFile . '.png');
        unlink($imagickFile . '.png');
    }

//    public function testGrayscale()
//    {
//        $obj = $this->getImage('imagick');
//        $obj->grayscale();
//        echo $obj->getImage()->getImageColorspace(), '<br>', \imagick::COLORSPACE_GRAY;
//        $this->assertEquals($obj->getImage()->getImageColorspace(), \imagick::COLORSPACE_GRAY);
//    }
}
