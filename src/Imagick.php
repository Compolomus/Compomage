<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

class Imagick extends AbstractImage implements ImageInterface
{
    /**
     * Imagick constructor.
     * @param string $image
     * @throws \Exception
     */
    public function __construct(string $image)
    {
        $this->init($image);
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     */
    public function resize(int $width, int $height): ImageInterface
    {
        $this->getImage()->scaleImage($width, $height, false);
        $this->setSizes();

        return $this;
    }

    /**
     * @param int $angle
     * @return ImageInterface
     */
    public function rotate(int $angle = 90): ImageInterface
    {
        $this->getImage()->rotateImage(new \ImagickPixel('transparent'), $angle);
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
        $this->getImage()->modulateImage(100, 0, 100);

        return $this;
    }

    /**
     * @param string $text
     * @param string $position
     * @param string $font
     * @return $this
     * @throws \Exception
     */
    public function copyright(string $text, string $font = 'Courier', string $position = 'SouthWest')
    {
        $positions = [
            'NORTHWEST' => \Imagick::GRAVITY_NORTHWEST,
            'NORTH' => \Imagick::GRAVITY_NORTH,
            'NORTHEAST' => \Imagick::GRAVITY_NORTHEAST,
            'WEST' => \Imagick::GRAVITY_WEST,
            'CENTER' => \Imagick::GRAVITY_CENTER,
            'SOUTHWEST' => \Imagick::GRAVITY_SOUTHWEST,
            'SOUTH' => \Imagick::GRAVITY_SOUTH,
            'SOUTHEAST' => \Imagick::GRAVITY_SOUTHEAST,
            'EAST' => \Imagick::GRAVITY_EAST
        ];
        if (!\in_array($font, $this->getFontsList(), true)) {
            throw new \InvalidArgumentException('Does not support font');
        }
        if (!array_key_exists(strtoupper($position), $positions)) {
            throw new \InvalidArgumentException('Wrong position');
        }
        $this->getImage()->compositeImage($this->prepareImage($text, $positions[strtoupper($position)], $font),
            \Imagick::COMPOSITE_DISSOLVE, 0, 0);

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
     * @return \Imagick
     * @throws \ImagickException
     */
    private function prepareImage(string $text, int $position, string $font): \Imagick
    {
        $image = new \Imagick();
        $mask = new \Imagick();
        $draw = new \ImagickDraw();
        $image->newImage($this->getWidth(), $this->getHeight(), new \ImagickPixel('grey30'));
        $mask->newImage($this->getWidth(), $this->getHeight(), new \ImagickPixel('black'));
        $draw->setFont($font);
        $draw->setFontSize(20);
        $draw->setFillColor(new \ImagickPixel('grey70'));
        $draw->setGravity($position);
        $image->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor(new \ImagickPixel('white'));
        $mask->annotateImage($draw, 11, 13, 0, $text);
        $mask->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor(new \ImagickPixel('black'));
        $mask->annotateImage($draw, 9, 11, 0, $text);
        $mask->setImageMatte(false);
        $image->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        return $image;
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $startX
     * @param int $startY
     * @return ImageInterface
     */
    public function crop(int $width, int $height, int $startX, int $startY): ImageInterface
    {
        $this->getImage()->cropImage($width, $height, $startX, $startY);
        $this->setSizes();

        return $this;
    }

    public function save(string $filename): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return $this->getImage()->getImageBlob();
    }

    /**
     * @param string $source
     * @return ImageInterface
     * @throws \Exception
     */
    protected function tmp(string $source): ImageInterface
    {
        $image = new \Imagick;
        if ($image->readImageBlob($source)) {
            if ($image->getImageAlphaChannel() !== \Imagick::ALPHACHANNEL_ACTIVATE) {
                $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_SET);
            }
        }
        $background = $this->newImage($image->getImageWidth(), $image->getImageHeight());
        $image->compositeImage($background, \imagick::COMPOSITE_OVER, 0, 0); //Imagick::COMPOSITE_DISSOLVE
        $this->setImage($image);
        $this->getImage()->setFormat('png'); // save transparent
        $this->setSizes();

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @return \Imagick
     * @throws \ImagickException
     */
    private function newImage(int $width, int $height): \Imagick
    {
        $background = new \Imagick;
        $background->newImage($width, $height, new \ImagickPixel('transparent'));
        $background->setImageBackgroundColor(new \ImagickPixel('transparent'));

        return $background;
    }

    protected function setSizes(): void
    {
        $args = $this->getImage()->getImageGeometry();
        $this->setWidth($args['width']);
        $this->setHeight($args['height']);
    }

    /**
     * @param Image $watermark
     * @param int $x
     * @param int $y
     * @return ImageInterface
     * @throws \Exception
     */
    protected function prepareWatermark(Image $watermark, int $x, int $y): ImageInterface
    {
        $watermark->getImage()->evaluateImage(\Imagick::EVALUATE_MULTIPLY, 1, \Imagick::CHANNEL_ALPHA);
        $this->getImage()->compositeImage($watermark->getImage(), \Imagick::COMPOSITE_DISSOLVE, $x, $y);

        return $this;
    }
}
