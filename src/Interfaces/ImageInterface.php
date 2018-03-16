<?php declare(strict_types=1);

namespace Compolomus\Compomage\Interfaces;

interface ImageInterface
{
    public function save(string $filename): bool;

    public function resize(int $width, int $height): ImageInterface;

    public function crop(int $width, int $height, int $x, int $y): ImageInterface;

    public function rotate(int $angle = 90): ImageInterface;

    public function watermark(): ImageInterface;

    public function __toString(): string;
}
