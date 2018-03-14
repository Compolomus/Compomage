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
        $this->image->compositeImage($background, \imagick::COMPOSITE_OVER, 0, 0); //Imagick::COMPOSITE_DISSOLVE
//        $this->image->setFormat('png');
        $this->setImage($image);
        $this->setSizes();

        return $this;
    }

    protected function setSizes(): void
    {
        $args = $this->image->getImageGeometry();
        $this->setWidth($args['width']);
        $this->setHeight($args['height']);
    }

//    protected function setSizes(): void
//    {
//        $this->setWidth($this->image->getImageWidth());
//        $this->setHeight($this->image->getImageHeight());
//    }
}
