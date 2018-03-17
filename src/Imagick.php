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
        $background = new \Imagick;
        $background->newImage($image->getImageWidth(), $image->getImageHeight(), new \ImagickPixel('transparent'));
        $background->setImageBackgroundColor(new \ImagickPixel('transparent'));
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

    public function resize(int $width, int $height): ImageInterface
    {
        $this->getImage()->scaleImage($width, $height, false);
        $this->setSizes();

        return $this;
    }

    public function rotate(int $angle = 90): ImageInterface
    {
        $this->image->rotateImage(new \ImagickPixel('transparent'), $angle);
        $this->setSizes();

        return $this;
    }

    public function watermark(): ImageInterface
    {

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
