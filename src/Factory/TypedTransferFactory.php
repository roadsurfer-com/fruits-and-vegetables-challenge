<?php

declare(strict_types=1);

namespace App\Factory;

use App\Collection\Collection;
use App\Entity\Entity;

interface TypedTransferFactory
{
    public static function createEntity(array $data): Entity;

    public static function createCollection(): Collection;
}
