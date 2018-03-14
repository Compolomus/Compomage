<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;
use Compolomus\Compomage\Traits\ImageTrait;

class GD extends AbstractImage implements ImageInterface
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
        $image = imagecreatefromstring($source);
        if (!is_resource($image)) {
            throw new \Exception('Image create failed');
        }

        $this->setImage($image);
        $this->setSizes();

        // save transparent
        imagesavealpha($this->image, true);
        imagealphablending($this->image, false);

        return $this;
    }

    protected function setSizes(): void
    {
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }
}
