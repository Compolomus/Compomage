<?php declare(strict_types=1);

namespace Compolomus\Compomage\Interfaces;

use Compolomus\Compomage\Image;
use Imagick;

interface ImageInterface
{
    /**
     * Save file
     *
     * @param string $filename
     * @return bool
     */
    public function save(string $filename): bool;

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     */
    public function resize(int $width, int $height): ImageInterface;

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return ImageInterface
     */
    public function crop(int $width, int $height, int $x, int $y): ImageInterface;

    /**
     * @param int $level
     * @return ImageInterface
     */
    public function brightness(int $level): ImageInterface;

    /**
     * @param int $level
     * @return ImageInterface
     */
    public function contrast(int $level): ImageInterface;

    /**
     * @return ImageInterface
     */
    public function negate(): ImageInterface;

    /**
     * @return ImageInterface
     */
    public function blur(): ImageInterface;

    /**
     * @return ImageInterface
     */
    public function grayscale(): ImageInterface;

    /**
     * @return ImageInterface
     */
    public function flip(): ImageInterface;

    /**
     * @return ImageInterface
     */
    public function flop(): ImageInterface;

    /**
     * @param int $angle
     * @return ImageInterface
     */
    public function rotate(int $angle = 90): ImageInterface;

    /**
     * @param ImageInterface|Image $watermark
     * @param string $position
     * @return ImageInterface
     */
    public function watermark($watermark, string $position): ImageInterface;

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     */
    public function thumbnail(int $width, int $height): ImageInterface;

    /**
     * @return Imagick|resource
     */
    public function getImage();

    public function resizeByTransparentBackground(int $width, int $height): ImageInterface;

    public function resizeByBlurBackground(int $width, int $height): ImageInterface;

    /**
     * @return string
     */
    public function __toString(): string;
}
