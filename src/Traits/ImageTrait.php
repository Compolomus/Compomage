<?php

namespace Compolomus\Compomage\Traits;

use Compolomus\Compomage\Interfaces\ImageInterface;

trait ImageTrait
{
    /**
     * @param string $image
     * @throws \Exception
     */
    protected function init(string $image)
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
    protected function getImageByURL(string $url): ?\Exception
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

    /**
     * @param string $base64
     * @throws \Exception
     */
    protected function getImageByBase64(string $base64): void
    {
        $this->tmp(base64_decode($base64));
    }

    protected function setImage($image): void
    {
        $this->image = $image;
    }

    protected function getImage()
    {
        return $this->image;
    }

    protected function getWidth(): int
    {
        return $this->width;
    }

    protected function setWidth(int $width): void
    {
        $this->width = $width;
    }

    protected function getHeight(): int
    {
        return $this->height;
    }

    protected function setHeight(int $height): void
    {
        $this->height = $height;
    }
}
