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
     * @param string $source
     * @return ImageInterface
     * @throws \Exception
     */
    protected function tmp(string $source): ImageInterface
    {
        $image = new \Imagick;
        if ($image->readImageBlob($source)) {
            if ($image->getImageAlphaChannel() !== \Imagick::ALPHACHANNEL_ACTIVATE) {
                $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_SET);  // 8
                #$image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_OPAQUE); // 6
            }
        }
        $background = $this->newImage($image->getImageWidth(), $image->getImageHeight());
        $image->compositeImage($background, \imagick::COMPOSITE_OVER, 0, 0); //Imagick::COMPOSITE_DISSOLVE
        $this->setImage($image);
        $this->getImage()->setFormat('png'); // save transparent
        $this->setSizes();

        return $this;
    }

    protected function setSizes(): void
    {
        $args = $this->getImage()->getImageGeometry();
        $this->setWidth($args['width']);
        $this->setHeight($args['height']);
    }

    private function newImage(int $width, int $height)
    {
        $background = new \Imagick;
        $background->newImage($width, $height, new \ImagickPixel('transparent'));
        $background->setImageBackgroundColor(new \ImagickPixel('transparent'));

        return $background;
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     * @throws \ImagickException
     */
    public function resize(int $width, int $height): ImageInterface
    {
        $this->getImage()->scaleImage($width, $height, false);
        $this->setSizes();

        return $this;
    }

    public function rotate(int $angle = 90): ImageInterface
    {
        $this->getImage()->rotateImage(new \ImagickPixel('transparent'), $angle);
        $this->setSizes();

        return $this;
    }

    public function watermark(): ImageInterface
    {
        return $this;
    }

    public function flip(): ImageInterface
    {
        $this->getImage()->flipImage();

        return $this;
    }

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

    public function getFontsList(): array
    {
        return $this->getImage()->queryFonts();
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

        if (!in_array($font, $this->getFontsList())) {
            throw new \Exception('Does not support font');
        }
        if (!array_key_exists(strtoupper($position), $positions)) {
            throw new \Exception('Wrong position');
        }

        $position = $positions[strtoupper($position)];

        $image = $this->prepareImage($text, $position, $font);
        $this->getImage()->compositeImage($image, \Imagick::COMPOSITE_DISSOLVE, 0, 0);

        return $this;
    }


    /**
     * @param string $text
     * @param int $position
     * @param string $font
     * @return \Imagick
     */
    private function prepareImage(string $text, int $position, string $font)
    {
        $image = new \Imagick();
        $mask = new \Imagick();
        $draw = new \ImagickDraw();

        $width = $this->getWidth();
        $height = $this->getHeight();

        $image->newImage($width, $height, new \ImagickPixel('grey30'));
        $mask->newImage($width, $height, new \ImagickPixel('black'));

        $draw->setFont($font);
        $draw->setFontSize(20);
        $draw->setFillColor('grey70');

        $draw->setGravity($position);

        $image->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor('white');
        $mask->annotateImage($draw, 11, 13, 0, $text);
        $mask->annotateImage($draw, 10, 12, 0, $text);
        $draw->setFillColor('black');
        $mask->annotateImage($draw, 9, 11, 0, $text);
        $mask->setImageMatte(false);

        $image->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        return $image;
    }

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
}
