<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Exception;
use InvalidArgumentException;

/**
 * Class Image
 * @package Compolomus\Compomage
 * @method Image save(string $filename): bool
 * @method Image __toString(): string
  * @method Image getBase64(): string
 * @method Image resize(int $width, int $height): ImageInterface
 * @method Image resizeByHeight(int $height): ImageInterface
 * @method Image resizeByWidth(int $width): ImageInterface
 * @method Image resizeByPercent(int $percent): ImageInterface
 * @method Image resizeBy(string $mode, int $param): ImageInterface
 * @method Image resizeByTransparentBackground(int $width, int $height): ImageInterface
 * @method Image resizeByBlurBackground(int $width, int $height): ImageInterface
 * @method Image crop(int $width, int $height, int $x, int $y): ImageInterface
 * @method Image rotate(int $angle = 90): ImageInterface
 * @method Image flip(): ImageInterface
 * @method Image flop(): ImageInterface
 * @method Image brightness(int $level): ImageInterface
 * @method Image contrast(int $level): ImageInterface
 * @method Image negate(): ImageInterface
 * @method Image blur(): ImageInterface
 * @method Image grayscale(): ImageInterface
 * @method Image getImage()
 * @method Image getWidth(): int
 * @method Image getHeight(): int
 * @method Image watermark($watermark, string $position): ImageInterface
 * @method Image thumbnail(int $width, int $height): ImageInterface
 */

class Image
{
    public const AUTO = 0;

    public const GD = 1;

    public const IMAGICK = 2;

    private $class = self::GD;

    private $object;

    /**
     * Image constructor.
     * @param string $filename
     * @param int $mode
     * @throws Exception
     */
    public function __construct(string $filename, $mode = self::AUTO)
    {
        $this->check($filename, $mode);
    }

    /**
     * @param string $filename
     * @param int $mode
     * @throws Exception
     */
    private function check(string $filename, $mode = self::AUTO): void
    {
        if ($mode === self::IMAGICK || ($mode === self::AUTO && extension_loaded('imagick') === true)) {
            $this->class = self::IMAGICK;
            $this->object = new Imagick2($filename);
            return;
        }
        $this->object = new GD($filename);
    }

    /**
     * @param string $method
     * @param $args
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __call(string $method, $args)
    {
        if (!method_exists($this->object, $method)) {
            throw new InvalidArgumentException('Undefined method ' . $method);
        }
        return $this->object->$method(...$args);
    }
}
