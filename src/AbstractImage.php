<?php

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

abstract class AbstractImage
{
    protected const POSITIONS = [
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

    public function getImage()
    {
        return $this->image;
    }

    protected function setImage($image): void
    {
        $this->image = $image;
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
                throw new \InvalidArgumentException('Unsupported mode type by resize');
        }
    }

    /**
     * @param int $width
     * @return ImageInterface
     */
    public function resizeByWidth(int $width): ImageInterface
    {
        return $this->resize($width, $this->getHeight() * ($width / $this->getWidth()));
    }

    abstract protected function resize(int $width, int $height): ImageInterface;

    public function getHeight(): int
    {
        return $this->height;
    }

    protected function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    protected function setWidth(int $width): void
    {
        $this->width = $width;
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
     * @param Image $watermark
     * @param string $position
     * @return ImageInterface
     * @throws \Exception
     */
    public function watermark(Image $watermark, string $position): ImageInterface
    {
        if (!array_key_exists(strtoupper($position), self::POSITIONS)) {
            throw new \InvalidArgumentException('Wrong position');
        }

        return $this->prepareWatermark(
            $watermark,
            (int) ((($this->getWidth() - $watermark->getWidth()) / 2) * self::POSITIONS[strtoupper($position)]['x']) + self::POSITIONS[strtoupper($position)]['padX'],
            (int) ((($this->getHeight() - $watermark->getHeight()) / 2) * self::POSITIONS[strtoupper($position)]['y']) + self::POSITIONS[strtoupper($position)]['padY']
        );
    }

    abstract protected function prepareWatermark(Image $watermark, int $x, int $y): ImageInterface;

    public function getBase64(): string
    {
        return chunk_split(base64_encode($this->__toString()));

    }

    abstract public function __toString(): string;

    abstract protected function setSizes(): void;

    /**
     * @param string $image
     * @throws \Exception
     */
    protected function init(string $image): void
    {
        switch ($image) {
            // base64
            case base64_encode(base64_decode($image, true)) === $image:
                $this->getImageByBase64($image);
                break;
            // URL
            case (0 === strpos($image, 'http')):
                $this->getImageByURL($image);
                break;
            // Local file
            default:
                $this->tmp(file_get_contents($image));
        }
    }

    /**
     * @param string $base64
     * @throws \Exception
     */
    protected function getImageByBase64(string $base64): void
    {
        $this->tmp(base64_decode($base64));
    }

    abstract protected function tmp(string $source): ImageInterface;

    /**
     * @param string $url
     * @return \Exception|null
     * @throws \Exception
     */
    protected function getImageByURL(string $url): ?\Exception
    {
        if (getimagesize($url)) {
            $upload = new \SplFileObject($url, 'rb');
            $image = '';
            while (!$upload->eof()) {
                $image .= $upload->fgets();
            }
        } else {
            throw new \InvalidArgumentException('Unsupported image type');
        }
        $this->tmp($image);

        return null;
    }
}
