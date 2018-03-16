<?php

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

abstract class AbstractImage
{
    protected $image;

    protected $width;

    protected $height;

    abstract protected function getImage();

    abstract protected function setImage($image): void;

    abstract protected function getWidth(): int;

    abstract protected function setWidth(int $width): void;

    abstract protected function getHeight(): int;

    abstract protected function setHeight(int $height): void;

    abstract protected function setSizes(): void;

    abstract protected function resizeByPercent(int $percent): ImageInterface;

    abstract protected function resizeByHeight(int $height): ImageInterface;

    abstract protected function resizeByWidth(int $width): ImageInterface;

    abstract protected function tmp(string $source): ImageInterface;
}
