<?php declare(strict_types=1);

namespace Compolomus\Compomage;

/**
 * Class Image
 * @package Compolomus\Compomage
 * @method Image save(string $filename): bool
 * @method Image resize(int $width, int $height): ImageInterface
 * @method Image crop(int $width, int $height, int $x, int $y): ImageInterface
 * @method Image rotate(int $angle = 90): ImageInterface
 * @method Image watermark(): ImageInterface
 * @method Image __toString(): string
 * @method Image resizeByHeight(int $height): ImageInterface
 * @method Image resizeByWidth(int $width): ImageInterface
 * @method Image resizeByPercent(int $percent): ImageInterface
 * @method Image resizeBy(string $mode, int $param): ImageInterface
 * @method Image getBase64(): string
 */

class Image
{
    const AUTO = 0;

    const GD = 1;

    const IMAGICK = 2;

    private $class = self::GD;

    private $object;

    /**
     * Image constructor.
     * @param string $filename
     * @param int $mode
     * @throws \Exception
     */
    public function __construct(string $filename, $mode = self::AUTO)
    {
        $this->check($filename, $mode);
    }

    /**
     * @param string $filename
     * @param int $mode
     * @throws \Exception
     */
    private function check(string $filename, $mode = self::AUTO)
    {
        if ($mode == self::IMAGICK || $mode == self::AUTO && extension_loaded('imagick') === true) {
            $this->class = self::IMAGICK;
            $this->object = new Imagick($filename);
            return;
        }
        $this->object = new GD($filename);
    }

    /**
     * @param string $method
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call(string $method, $args)
    {
//        return $this->object->$method(...$args);
        if (!method_exists($this->object, $method)) {
            throw new \Exception('Undefined method ' . $method);
        }
        return call_user_func_array([$this->object, $method], $args);
    }
}