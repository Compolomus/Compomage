<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;

class GD extends AbstractImage implements ImageInterface
{
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
     * @param string $source
     * @return ImageInterface
     * @throws \Exception
     */
    protected function tmp(string $source): ImageInterface
    {
        $image = imagecreatefromstring($source);
        if (!is_resource($image)) {
            throw new \Exception('Image create failed');
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

    public function copyright(string $text, string $font, string $position = 'SouthWest')
    {
        $positions = [
            'NORTHWEST' => ['x' => 0, 'y' => 0], 'NORTH'  => ['x' => 1, 'y' => 0], 'NORTHEAST' => ['x' => 2, 'y' => 0],
            'WEST'      => ['x' => 0, 'y' => 1], 'CENTER' => ['x' => 1, 'y' => 1], 'EAST'      => ['x' => 2, 'y' => 1],
            'SOUTHWEST' => ['x' => 0, 'y' => 2], 'SOUTH'  => ['x' => 1, 'y' => 2], 'SOUTHEAST' => ['x' => 2, 'y' => 2]
        ];

        $fontSize = 14;

        $coordinates = imagettfbbox($fontSize, 0, $font, $text);
        $minX = min(array($coordinates[0],$coordinates[2],$coordinates[4],$coordinates[6]));
        $maxX = max(array($coordinates[0],$coordinates[2],$coordinates[4],$coordinates[6]));
        $minY = min(array($coordinates[1],$coordinates[3],$coordinates[5],$coordinates[7]));
        $maxY = max(array($coordinates[1],$coordinates[3],$coordinates[5],$coordinates[7]));

        $textX = abs($minX) + 1;
        $textY = abs($minY) + 1;
        $nWidth = $maxX - $minX + 3;
        $nHeight = $maxY - $minY + 3;

        $image = $this->newImage($nWidth, $nHeight);
        $white = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 128, 128);
        $red = imagecolorallocate($image, 255, 0, 0);
        $black = imagecolorallocate($image, 255, 255, 255);
        $gray = imagecolorallocate($image, 128, 128, 128);
        imagecolortransparent($image, $white);
        imagefilledrectangle($image, 0, 0, $this->getWidth(), 20, $white);

        imagettftext($image, $fontSize, 0, $textX, $textY, $white, $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 1, $textY + 1, $blue, $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 1, $textY + 1, $red, $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $black, $font, $text);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $gray, $font, $text);

        imagecopymerge(
            $this->getImage(),
            $image,
            intval((($this->getWidth() - imagesx($image)) / 2) * $positions[strtoupper($position)]['x']),
            intval((($this->getHeight() - imagesy($image)) / 2) * $positions[strtoupper($position)]['y']),
            0,
            0,
            $this->getWidth(),
            $this->getHeight(),
            60
        );

        return $this;
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
        $width = $width - $x;
        $height = $height - $y;
        $newimage = $this->newImage($width, $height);
        imagecopyresampled($newimage, $this->getImage(), 0, 0, $x, $y, $width, $height, $width, $height);
        $this->setImage($newimage);
        $this->setSizes();

        return $this;
    }

    public function watermark(): ImageInterface
    {
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
