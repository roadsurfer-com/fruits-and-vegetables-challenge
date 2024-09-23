<?php

declare(strict_types=1);

namespace App\Collection;

use App\Entity\Entity;
use App\Entity\Fruit;
use ArrayIterator;
use ArrayObject;

final class FruitCollection extends AbstractCollection implements Collection
{
    public const TYPE = 'fruit';
}
