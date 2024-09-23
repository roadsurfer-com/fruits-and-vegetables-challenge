<?php

declare(strict_types=1);

namespace App\Collection;

use App\Entity\Entity;
use ArrayIterator;

interface Collection
{
    public const TYPE = '';

    public function add(Entity $entity): void;

    public function list(): ArrayIterator;

    public function remove(int $id): void;
}
