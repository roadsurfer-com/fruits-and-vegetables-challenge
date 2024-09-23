<?php

declare(strict_types=1);

namespace App\Factory;

use App\Collection\Collection;
use App\Collection\VegetableCollection;
use App\Entity\Entity;
use App\Entity\Vegetable;

final class VegetableFactory implements TypedTransferFactory
{
    /**
     * @param array<string,string|float|int> $data
     *
     * @return Entity&Vegetable
     */
    public static function createEntity(array $data): Entity
    {
        $aVegetable = new Vegetable();

        if (isset($data['id'])) {
            $aVegetable->setId((int)$data['id']);
        }

        $aVegetable->setName((string)$data['name']);
        $aVegetable->setQuantity((float)$data['quantity']);

        return $aVegetable;
    }

    /**
     * @return Collection&VegetableCollection
     */
    public static function createCollection(): Collection
    {
        return new VegetableCollection();
    }
}
