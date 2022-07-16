<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;
use Exception;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use InvalidArgumentException;

class Imagick2 extends AbstractImage
{
    /**
     * Imagick constructor.
     * @param string $image
     * @throws Exception
     */
    public function __construct(string $image)
    {
        $this->init($image);
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     * @throws ImagickException
     */
    public function resize(int $width, int $height): ImageInterface
    {
        $this->getImage()->scaleImage($width, $height, false);
        $this->setSizes();

        return $this;
    }

    protected function setSizes(): void
    {
        $args = $this->getImage()->getImageGeometry();
        $this->setWidth($args['width']);
        $this->setHeight($args['height']);
        $this->setOrientation();
    }

    /**
     * @param int $angle
     * @return ImageInterface
     */
    public function rotate(int $angle = 90): ImageInterface
    {
        $this->getImage()->rotateImage(new ImagickPixel('transparent'), $angle);
        $this->setSizes();

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function flip(): ImageInterface
    {
        $this->getImage()->flipImage();

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function flop(): ImageInterface
    {
        $this->getImage()->flopImage();

        return $this;
    }

    public function grayscale(): ImageInterface
    {
        $this->getImage()->transformimagecolorspace(Imagick::COLORSPACE_GRAY);
        $this->getImage()->separateImageChannel(1);

        // modulateImage(100, 0, 100);

        return $this;
    }

    /**
     * @param string $text
     * @param string $position
     * @param string $font
     * @return ImageInterface
     * @throws InvalidArgumentException
     * @throws ImagickException
     */
    public function copyright(string $text, string $font = 'Courier', string $position = 'SouthWest'): ImageInterface
    {
        $positions = [
            'NORTHWEST' => Imagick::GRAVITY_NORTHWEST,
            'NORTH' => Imagick::GRAVITY_NORTH,
            'NORTHEAST' => Imagick::GRAVITY_NORTHEAST,
            'WEST' => Imagick::GRAVITY_WEST,
            'CENTER' => Imagick::GRAVITY_CENTER,
            'SOUTHWEST' => Imagick::GRAVITY_SOUTHWEST,
            'SOUTH' => Imagick::GRAVITY_SOUTH,
            'SOUTHEAST' => Imagick::GRAVITY_SOUTHEAST,
            'EAST' => Imagick::GRAVITY_EAST
        ];
        if (!array_key_exists(strtoupper($position), $positions) || !in_array($font, $this->getFontsList(), true)) {
            throw new InvalidArgumentException('Does not support font or wrong position');
        }
        $this->getImage()->compositeImage($this->prepareImage($text, $positions[strtoupper($position)], $font),
            Imagick::COMPOSITE_DISSOLVE, 0, 0);

        return $this;
    }

    public function getFontsList(): array
    {
        return $this->getImage()->queryFonts();
    }

    /**
     * @param string $text
     * @param int $position
     * @param string $font
     * @return Imagick
     * @throws ImagickException
     */
    private function prepareImage(string $text, int $position, string $font): Imagick
    {
        $image = new Imagick();
        $mask = new Imagick();
        $draw = new ImagickDraw();
        $image->newImage($this->getWidth(), $this->getHeight(), new ImagickPixel('grey30'));
        $mask->newImage($this->getWidth(), $this->getHeight(), new ImagickPixel('black'));
        $draw->setFont($font);
        $draw->setFontSize(20);
        $draw->setFillColor(new ImagickPixel('grey70'));
        $draw->setGravity($position);
        $image->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor(new ImagickPixel('white'));
        $mask->annotateImage($draw, 11, 13, 0, $text);
        $mask->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor(new ImagickPixel('black'));
        $mask->annotateImage($draw, 9, 11, 0, $text);
        $mask->setImageMatte(false);
        $image->compositeImage($mask, Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        return $image;
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return ImageInterface
     * @throws ImagickException
     */

    public function crop(int $width, int $height, int $x, int $y): ImageInterface
    {
        $width -= $x;
        $height -= $y;

        $this->getImage()->cropImage($width, $height, $x, $y);
        $this->setSizes();

        return $this;
    }

    public function save(string $filename, $quality = 100): bool
    {
        $this->getImage()->setImageCompressionQuality($quality);
        $this->getImage()->setImageFormat('png');
        #$this->getImage()->writeImage($filename . '.png'); // bug
        file_put_contents ($filename. '.png', $this->getImage());

        return true;
    }

    public function __toString(): string
    {
        return trim($this->getImage()->getImageBlob());
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     * @throws ImagickException
     */
    protected function prepareThumbnail(int $width, int $height): ImageInterface
    {
        $this->getImage()->cropThumbnailImage($width, $height);

        return $this;
    }

    /**
     * @param string $source
     * @return ImageInterface
     * @throws ImagickException
     */
    protected function tmp(string $source): ImageInterface
    {
        $image = new Imagick;
        if ($image->readImageBlob($source)) {
            if ($image->getImageAlphaChannel() !== Imagick::ALPHACHANNEL_ACTIVATE) {
                $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
            }
        }
        $background = $this->newImage($image->getImageWidth(), $image->getImageHeight());
        $image->compositeImage($background, Imagick::COMPOSITE_OVER, 0, 0); //Imagick::COMPOSITE_DISSOLVE
        $this->setImage($image);
        $this->getImage()->setFormat('png'); // save transparent
        $this->setSizes();

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @return Imagick
     * @throws ImagickException
     */
    protected function newImage(int $width, int $height): Imagick
    {
        $background = new Imagick;
        $background->newImage($width, $height, 'none');
        $background->setImageAlphaChannel(Imagick::ALPHACHANNEL_TRANSPARENT);

        return $background;
    }

    /**
     * @param Image $watermark
     * @param int $x
     * @param int $y
     * @return ImageInterface
     * @throws Exception
     */
    protected function prepareWatermark($watermark, int $x, int $y): ImageInterface
    {
        $watermark = $watermark->getImage();
        $watermark->evaluateImage(Imagick::EVALUATE_MULTIPLY, 1, Imagick::CHANNEL_ALPHA);
        $this->getImage()->compositeImage($watermark, Imagick::COMPOSITE_DISSOLVE, $x, $y);

        return $this;
    }

    public function resizeByTransparentBackground(int $width, int $height): ImageInterface
    {
        $background = $this->newImage($width, $height);
        $background->setImageFormat('png');

        $w = (int) (($width - $this->getWidth()) / 2);
        $h = (int) (($height - $this->getHeight()) / 2);

        $background->compositeImage($this->getImage(), Imagick::COMPOSITE_DISSOLVE, $w, $h);

        return $this->setBackground($width, $height, new Image(base64_encode((string) $background), Image::IMAGICK));
    }

    public function resizeByBlurBackground(int $width, int $height): ImageInterface
    {
        $background = new Image(base64_encode((string) $this));
        $background->resize($width, $height)->blur();

        return $this->setBackground($width, $height, $background);
    }

    /**
     * @param int $level
     * @return ImageInterface
     */
    public function brightness(int $level): ImageInterface
    {
        if (!$this->compareRangeValue($level, 200))
        {
            throw new InvalidArgumentException('Wrong brightness level, range 0 - 200, ' . $level . ' given');
        }
        $this->getImage()->modulateImage(abs($level), 100, 100);

        return $this;
    }

    /**
     * @param int $level
     * @return ImageInterface
     */
    public function contrast(int $level): ImageInterface
    {
        if (!$this->compareRangeValue($level, 100))
        {
            throw new InvalidArgumentException('Wrong contrast level, range 0 - 100, ' . $level . ' given');
        }
        $this->getImage()->brightnessContrastImage(0, abs($level));

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function negate(): ImageInterface
    {
        $this->getImage()->negateImage(false);

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function blur(): ImageInterface
    {
        $this->getImage()->blurImage(7, 5);

        return $this;
    }
}
