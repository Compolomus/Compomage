<?php declare(strict_types=1);

namespace Compolomus\Compomage;

use Compolomus\Compomage\Interfaces\ImageInterface;
use Compolomus\Compomage\Traits\ImageTrait;

class GD extends AbstractImage implements ImageInterface
{
    use ImageTrait;
}
