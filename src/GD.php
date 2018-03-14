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
        switch ($image) {
            // base64
            case (preg_match('^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$',
                $image) ? true : false) :
                $this->getImageByBase64($image);
                break;
            // URL
            case (substr($image, 0, 4) == 'http' ? true : false) :
                $this->getImageByURL($image);
                break;
            // Local file
            default:
                $this->tmp(file_get_contents($image));
        }
    }

    /**
     * @param string $url
     * @return \Exception|null
     * @throws \Exception
     */
    private function getImageByURL(string $url): ?\Exception
    {
        if (list(, , $type) = getimagesize($url)) {
            if ($type) {
                $upload = new \SplFileObject($url, 'rb');
                $image = '';
                while (!$upload->eof()) {
                    $image .= $upload->fgets();
                }
            } else {
                throw new \Exception('Unsupported image type');
            }
        } else {
            throw new \Exception('Unsupported image type');
        }
        $this->tmp($image);
    }

    private function getImageByBase64(string $base64): void
    {
        $this->tmp(base64_decode($base64));
    }

    protected function setImage($image): void
    {
        $this->image = $image;
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

        $this->setWidth(imagesx($image));
        $this->setHeight(imagesy($image));
        $this->setImage($image);

        // save transparent
        imagesavealpha($this->image, true);
        imagealphablending($this->image, false);

        return $this;
    }
}
