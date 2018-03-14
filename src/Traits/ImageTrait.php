<?php

namespace Compolomus\Compomage\Traits;

use Compolomus\Compomage\Interfaces\ImageInterface;

trait ImageTrait
{
    protected function getImage(): ImageInterface
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
