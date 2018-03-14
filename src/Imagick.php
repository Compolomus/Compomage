<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;
use Compolomus\Compomage\Traits\ImageTrait;

class Imagick extends AbstractImage implements ImageInterface
{
    use ImageTrait;

    /**
     * GD constructor.
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
        $this->image = new \Imagick;
        if ($this->image->readImageBlob($source)) {
            if ($this->image->getImageAlphaChannel() !== \Imagick::ALPHACHANNEL_ACTIVATE) {
                $this->image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_SET);  // 8
                #$this->image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_OPAQUE); // 6
            }
        }
        $background = new \Imagick;
        $background->newImage($this->image->getImageWidth(), $this->image->getImageHeight(), new \ImagickPixel('transparent'));
        $background->setImageBackgroundColor(new \ImagickPixel('transparent'));
        $this->image->compositeImage($background, \imagick::COMPOSITE_OVER, 0, 0); //Imagick::COMPOSITE_DISSOLVE
//        $this->image->setFormat('png');
        $this->setSizes();

        return $this;
    }
}
