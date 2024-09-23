<?php

declare(strict_types=1);

namespace App\Factory;

use App\Collection\Collection;
use App\Collection\FruitCollection;
use App\Entity\Entity;
use App\Entity\Fruit;

final class FruitFactory implements TypedTransferFactory
{
    /**
     * @param array<string,mixed> $data
     *
     * @return Entity&Fruit
     */
    public static function createEntity(array $data): Entity
    {
        $aFruit = new Fruit();

        if (isset($data['id'])) {
            $aFruit->setId($data['id']);
        }

        $aFruit->setName($data['name']);
        $aFruit->setQuantity($data['quantity']);

        return $aFruit;
    }

    /**
     * @return Collection&FruitCollection
     */
    public static function createCollection(): Collection
    {
        return new FruitCollection();
    }
}
