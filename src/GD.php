<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;
use Exception;
use InvalidArgumentException;
use LogicException;
use RangeException;
use SplFileObject;

class GD extends AbstractImage
{
    /**
     * GD constructor.
     * @param string $image
     * @throws Exception
     */
    public function __construct(string $image)
    {
        $this->init($image);
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     * @throws Exception
     */
    public function resizeByTransparentBackground(int $width, int $height): ImageInterface
    {
        $temp = new SplFileObject(tempnam(sys_get_temp_dir(), 'image' . mt_rand()), 'w+');
        imagepng($this->newImage($width, $height), $temp->getRealPath(), 9, PNG_ALL_FILTERS);

        return $this->setBackground($width, $height, new Image($temp->getRealPath(), Image::GD));
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     * @throws Exception
     */
    public function resizeByBlurBackground(int $width, int $height): ImageInterface
    {
        $background = new Image(base64_encode((string) $this), Image::GD);
        $background->blur()->resize($width, $height);

        return $this->setBackground($width, $height, $background);
    }

    /**
     * @param int $level
     * @return ImageInterface
     */
    public function brightness(int $level): ImageInterface
    {
        if (!$this->compareRangeValue($level, 255))
        {
            throw new InvalidArgumentException('Wrong brightness level, range -255 - 255, ' . $level . ' given');
        }
        imagefilter($this->getImage(), IMG_FILTER_BRIGHTNESS, $level);

        return $this;
    }

    /**
     * @param int $level
     * @return ImageInterface
     */
    public function contrast(int $level): ImageInterface
    {
        if (!$this->compareRangeValue($level, 100))
        {
            throw new InvalidArgumentException('Wrong contrast level, range -100 - 100, ' . $level . ' given');
        }
        imagefilter($this->getImage(), IMG_FILTER_CONTRAST, $level);

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function negate(): ImageInterface
    {
        imagefilter($this->getImage(), IMG_FILTER_NEGATE);

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function blur(): ImageInterface
    {
        for ($i = 0; $i < 30; $i++) {
            if ($i % 5 === 0) {
                imagefilter($this->getImage(), IMG_FILTER_SMOOTH, -7);
            }
            imagefilter($this->getImage(), IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function flip(): ImageInterface
    {
        imageflip($this->getImage(), IMG_FLIP_VERTICAL);

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function flop(): ImageInterface
    {
        imageflip($this->getImage(), IMG_FLIP_HORIZONTAL);

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function grayscale(): ImageInterface
    {
        imagefilter($this->getImage(), IMG_FILTER_GRAYSCALE);

        return $this;
    }

    /**
     * @param string $text
     * @param string $font
     * @param string $position
     * @return $this
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function copyright(string $text, string $font, string $position = 'SouthWest'): ImageInterface
    {
        if (!array_key_exists(strtoupper($position), self::POSITIONS)) {
            throw new InvalidArgumentException('Wrong position');
        }

        imagecopymerge(
            $this->getImage(),
            $image = $this->prepareImage($text, $font),
            (int) ((($this->getWidth() - imagesx($image)) / 2) * self::POSITIONS[strtoupper($position)]['x']) + self::POSITIONS[strtoupper($position)]['padX'],
            (int) ((($this->getHeight() - imagesy($image)) / 2) * self::POSITIONS[strtoupper($position)]['y']) + self::POSITIONS[strtoupper($position)]['padY'],
            0,
            0,
            $this->getWidth(),
            $this->getHeight(),
            80
        );

        return $this;
    }

    /**
     * @param string $text
     * @param string $font
     * @return resource
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function prepareImage(string $text, string $font)
    {
        if (!$coordinates = imagettfbbox($fontSize = 15, 0, $font, $text)) {
            throw new InvalidArgumentException('Does not support font');
        }

        $minX = min([$coordinates[0], $coordinates[2], $coordinates[4], $coordinates[6]]);
        $maxX = max([$coordinates[0], $coordinates[2], $coordinates[4], $coordinates[6]]);
        $minY = min([$coordinates[1], $coordinates[3], $coordinates[5], $coordinates[7]]);
        $maxY = max([$coordinates[1], $coordinates[3], $coordinates[5], $coordinates[7]]);
        $textX = (int) abs($minX) + 1;
        $textY = (int) abs($minY) + 1;
        $image = $this->newImage($maxX - $minX + 2, $maxY - $minY + 2);
        imagecolortransparent($image, $white = imagecolorallocate($image, 0, 0, 0));
        imagefilledrectangle($image, 0, 0, $this->getWidth(), 20, $white);
        imagettftext($image, $fontSize, 0, $textX, $textY, $white, $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 1, $textY + 1, $blue = imagecolorallocate($image, 0, 128, 128),
            $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 1, $textY + 1, $red = imagecolorallocate($image, 255, 0, 0), $font,
            $text);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $black = imagecolorallocate($image, 255, 255, 255),
            $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $gray = imagecolorallocate($image, 128, 128, 128),
            $font, $text);

        return $image;
    }

    /**
     * @param int $width
     * @param int $height
     * @return resource
     */
    protected function newImage(int $width, int $height)
    {
        $newimage = imagecreatetruecolor($width, $height);
        $transparent = imagecolorallocatealpha($newimage, 255, 255, 255, 127);
        imagefill($newimage, 0, 0, $transparent);
        imagealphablending($newimage, true);
        imagesavealpha($newimage, true);

        return $newimage;
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageInterface
     */
    public function resize(int $width, int $height): ImageInterface
    {
        $newimage = $this->newImage($width, $height);
        imagecopyresampled($newimage, $this->getImage(), 0, 0, 0, 0, $width, $height, $this->getWidth(),
            $this->getHeight());
        $this->setImage($newimage);
        $this->setSizes();

        return $this;
    }

    protected function setSizes(): void
    {
        $this->setWidth(imagesx($this->getImage()));
        $this->setHeight(imagesy($this->getImage()));
        $this->setOrientation();
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return ImageInterface
     */
    public function crop(int $width, int $height, int $x, int $y): ImageInterface
    {
        $width -= $x;
        $height -= $y;
        $newimage = $this->newImage($width, $height);
        imagecopyresampled($newimage, $this->getImage(), 0, 0, $x, $y, $width, $height, $width, $height);
        $this->setImage($newimage);
        $this->setSizes();

        return $this;
    }

    /**
     * @param int $angle
     * @return ImageInterface
     */
    public function rotate(int $angle = 90): ImageInterface
    {
        $angle *= -1;
        $transparent = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
        $rotate = imagerotate($this->getImage(), $angle, $transparent);
        imagealphablending($rotate, true);
        imagesavealpha($rotate, true);
        $this->setImage($rotate);
        $this->setSizes();

        return $this;
    }

    /**
     * @throws LogicException
     * @throws RangeException
     * @return string
     */
    public function __toString(): string
    {
        $temp = new SplFileObject(tempnam(sys_get_temp_dir(), 'image' . mt_rand()), 'w+');
        imagepng($this->getImage(), $temp->getRealPath(), 9, PNG_ALL_FILTERS);

        return trim(file_get_contents($temp->getRealPath()));
    }

    /**
     * @param string $filename
     * @param int $quality
     * @return bool
     */
    public function save(string $filename, $quality = 100): bool
    {
        return imagepng($this->getImage(), $filename . '.png', (int)($quality / 11), PNG_ALL_FILTERS);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $newWidth
     * @param int $newHeight
     * @return ImageInterface
     */
    protected function prepareThumbnail(
        int $width,
        int $height,
        int $newWidth = 0,
        int $newHeight = 0
    ): ImageInterface {
        imagecopyresampled(
            $newimage = $this->newImage($width, $height),
            $this->getImage(),
            0 - (int) (($newWidth - $width) / 2),
            0 - (int) (($newHeight - $height) / 2),
            0,
            0,
            $newWidth,
            $newHeight,
            $this->getWidth(),
            $this->getHeight()
        );
        $this->setImage($newimage);
        $this->setSizes();

        return $this;
    }

    /**
     * @param string $source
     * @return ImageInterface
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function tmp(string $source): ImageInterface
    {
        if (@!is_resource($image = @imagecreatefromstring($source))) {
            throw new InvalidArgumentException('Image create failed');
        }
        $this->setImage($image);
        $this->setSizes();
        // save transparent
        imagesavealpha($this->getImage(), true);
        imagealphablending($this->getImage(), false);

        return $this;
    }

    /**
     * @param ImageInterface $watermark
     * @param int $x
     * @param int $y
     * @return ImageInterface
     */
    protected function prepareWatermark($watermark, int $x, int $y): ImageInterface
    {
        imagealphablending($this->getImage(), true);
        imagesavealpha($this->getImage(), true);
        imagecopyresampled(
            $this->getImage(),
            $watermark->getImage(),
            $x,
            $y,
            0,
            0,
            $width = $watermark->getWidth(),
            $height = $watermark->getHeight(),
            $width,
            $height
        );

        return $this;
    }
}
