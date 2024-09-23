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
     * @param array<string,mixed> $data
     *
     * @return Entity&Vegetable
     */
    public static function createEntity(array $data): Entity
    {
        $aVegetable = new Vegetable();

        if (isset($data['id'])) {
            $aVegetable->setId($data['id']);
        }

        $aVegetable->setName($data['name']);
        $aVegetable->setQuantity($data['quantity']);

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
