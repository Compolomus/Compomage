<?php

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

abstract class AbstractImage
{

    protected $positions = [
        'NORTHWEST' => ['x' => 0, 'y' => 0, 'padX' => 10, 'padY' => 10],
        'NORTH'     => ['x' => 1, 'y' => 0, 'padX' => 0, 'padY' => 10],
        'NORTHEAST' => ['x' => 2, 'y' => 0, 'padX' => -10, 'padY' => 10],
        'WEST'      => ['x' => 0, 'y' => 1, 'padX' => 10, 'padY' => 0],
        'CENTER'    => ['x' => 1, 'y' => 1, 'padX' => 0, 'padY' => 0],
        'EAST'      => ['x' => 2, 'y' => 1, 'padX' => -10, 'padY' => 0],
        'SOUTHWEST' => ['x' => 0, 'y' => 2, 'padX' => 10, 'padY' => -10],
        'SOUTH'     => ['x' => 1, 'y' => 2, 'padX' => 0, 'padY' => -10],
        'SOUTHEAST' => ['x' => 2, 'y' => 2, 'padX' => -10, 'padY' => -10]
    ];

    protected $image;

    protected $width;

    protected $height;

    abstract protected function setSizes(): void;

    abstract protected function resize(int $width, int $height): ImageInterface;

    abstract protected function prepareWatermark(Image $watermark, int $x, int $y): ImageInterface;

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
            case base64_encode(base64_decode($image, true)) == $image :
                $this->getImageByBase64($image);
                break;
            // URL
            case (substr($image, 0, 4) == 'http') :
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
        [, , $type] = getimagesize($url);
        if ($type) {
            $upload = new \SplFileObject($url, 'rb');
            $image = '';
            while (!$upload->eof()) {
                $image .= $upload->fgets();
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

    public function getImage()
    {
        return $this->image;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    protected function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    protected function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @param int $height
     * @return ImageInterface
     */
    public function resizeByHeight(int $height): ImageInterface
    {
        return $this->resize($this->getWidth() * ($height / $this->getHeight()), $height);
    }

    /**
     * @param int $width
     * @return ImageInterface
     */
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

    /**
     * @param Image $watermark
     * @param string $position
     * @return ImageInterface
     * @throws \Exception
     */
    public function watermark(Image $watermark, string $position): ImageInterface
    {
        if (!array_key_exists(strtoupper($position), $this->positions)) {
            throw new \Exception('Wrong position');
        }

        return $this->prepareWatermark(
            $watermark,
            intval((($this->getWidth() - $watermark->getWidth()) / 2) * $this->positions[strtoupper($position)]['x']) + $this->positions[strtoupper($position)]['padX'],
            intval((($this->getHeight() - $watermark->getHeight()) / 2) * $this->positions[strtoupper($position)]['y']) + $this->positions[strtoupper($position)]['padY']
        );
    }

    public function getBase64(): string
    {
        return chunk_split(base64_encode($this->__toString()));

    }
}
