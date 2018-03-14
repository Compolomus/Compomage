<?php declare(strict_types=1);

namespace Compolomus\Compomage\Interfaces;

interface ImageInterface
{
    public function save(string $filename): bool;

    public function resize();

    public function rotate(int $angle);

    public function watermark();

    public function crop();

    public function scale();
}
