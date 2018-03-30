<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

class GD extends AbstractImage implements ImageInterface
{
    /**
     * @var resource
     */
    private $image;

    /**
     * GD constructor.
     * @param string $image
     * @throws \Exception
     */
    public function __construct(string $image)
    {
        $this->init($image);
    }

    /**
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @param string $source
     * @return ImageInterface
     * @throws \Exception
     */
    protected function tmp(string $source): ImageInterface
    {
        $image = imagecreatefromstring($source);
        if (!\is_resource($image)) {
            throw new \InvalidArgumentException('Image create failed');
        }
        $this->setImage($image);
        $this->setSizes();
        // save transparent
        imagesavealpha($this->getImage(), true);
        imagealphablending($this->getImage(), false);

        return $this;
    }

    public function flip(): ImageInterface
    {
        imageflip($this->getImage(), IMG_FLIP_VERTICAL);

        return $this;
    }

    public function flop(): ImageInterface
    {
        imageflip($this->getImage(), IMG_FLIP_HORIZONTAL);

        return $this;
    }

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
     * @throws \Exception
     */
    public function copyright(string $text, string $font, string $position = 'SouthWest'): ImageInterface
    {
        if (!array_key_exists(strtoupper($position), self::POSITIONS)) {
            throw new \InvalidArgumentException('Wrong position');
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
     * @throws \Exception
     */
    private function prepareImage(string $text, string $font)
    {
        if (!$coordinates = imagettfbbox($fontSize = 15, 0, $font, $text)) {
            throw new \InvalidArgumentException('Does not support font');
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
        imagettftext($image, $fontSize, 0, $textX + 1, $textY + 1, $blue = imagecolorallocate($image, 0, 128, 128), $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 1, $textY + 1, $red = imagecolorallocate($image, 255, 0, 0), $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $black = imagecolorallocate($image, 255, 255, 255), $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $gray = imagecolorallocate($image, 128, 128, 128), $font, $text);

        return $image;
    }

    protected function setSizes(): void
    {
        $this->setWidth(imagesx($this->getImage()));
        $this->setHeight(imagesy($this->getImage()));
    }

    private function newImage(int $width, int $height)
    {
        $newimg = imagecreatetruecolor($width, $height);
        $transparent = imagecolorallocatealpha($newimg, 255, 255, 255, 127);
        imagefill($newimg, 0, 0, $transparent);
        imagealphablending($newimg, true);
        imagesavealpha($newimg, true);

        return $newimg;
    }

    public function resize(int $width, int $height): ImageInterface
    {
        $newimage = $this->newImage($width, $height);
        imagecopyresampled($newimage, $this->getImage(), 0, 0, 0, 0, $width, $height, $this->getWidth() , $this->getHeight());
        $this->setImage($newimage);
        $this->setSizes();

        return $this;
    }

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
     * @param Image $watermark
     * @param int $x
     * @param int $y
     * @return ImageInterface
     */
    protected function prepareWatermark(Image $watermark, int $x, int $y): ImageInterface
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

    public function rotate(int $angle = 90): ImageInterface
    {
        $transparent = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
        $rotate = imagerotate($this->getImage(), $angle, $transparent);
        imagealphablending($rotate, true);
        imagesavealpha($rotate, true);
        $this->setImage($rotate);
        $this->setSizes();

        return $this;
    }

    public function __toString(): string
    {
        ob_start();
        imagepng($this->getImage(), null, 9, PNG_ALL_FILTERS);
        $temp = ob_get_contents();
        ob_clean();

        return trim($temp);
    }

    public function save(string $filename): bool
    {
        return true;
    }
}
