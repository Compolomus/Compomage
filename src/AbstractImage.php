<?php

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

abstract class AbstractImage
{
    protected $image;

    protected $width;

    protected $height;

    abstract protected function getImage(): ImageInterface;

    abstract protected function setImage(ImageInterface $image): void;

    abstract protected function getWidth(): int;

    abstract protected function setWidth(int $width): void;

    abstract protected function getHeight(): int;

    abstract protected function setHeight(int $height): void;

    abstract protected function setSizes(): void;

}
