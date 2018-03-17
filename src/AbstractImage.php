<?php

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

abstract class AbstractImage
{
    protected $image;

    protected $width;

    protected $height;

    abstract protected function setSizes(): void;

    abstract protected function flip(): ImageInterface;

    abstract protected function flop(): ImageInterface;

    abstract protected function grayscale(): ImageInterface;

    abstract protected function resize(int $width, int $height): ImageInterface;

    abstract protected function tmp(string $source): ImageInterface;

    abstract public function __toString(): string;

    /**
     * @param string $image
     * @throws \Exception
     */
    protected function init(string $image)
    {
        switch ($image) {
            // base64
            case (preg_match('#^[a-zA-Z0-9+/]+={0,2}$#',
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

        return null;
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

    public function resizeByHeight(int $height): ImageInterface
    {
        return $this->resize($this->getWidth() * ($height / $this->getHeight()), $height);
    }

    public function resizeByWidth(int $width): ImageInterface
    {
        return $this->resize($width, $this->getHeight() * ($width / $this->getWidth()));
    }

    /**
     * @param int $percent
     * @return ImageInterface
     */
    public function resizeByPercent(int $percent): ImageInterface
    {
        $width = $this->getWidth() * ($percent / 100);
        $height = $this->getHeight() * ($percent / 100);
        return $this->resize($width, $height);
    }

    /**
     * @param string $mode
     * @param int $param
     * @return ImageInterface
     * @throws \Exception
     */
    public function resizeBy(string $mode, int $param): ImageInterface
    {
        switch ($mode) {
            case 'width':
                return $this->resizeByWidth($param);
            case 'height':
                return $this->resizeByHeight($param);
            case 'percent':
                return $this->resizeByPercent($param);
            default:
                throw new \Exception('Unsupported mode type by resize');
        }
    }

    public function getBase64(): string
    {
        return chunk_split(base64_encode($this->__toString()));

    }
}
