<?php

declare(strict_types=1);

namespace App\Collection;

use App\Entity\Entity;
use App\Entity\Fruit;
use ArrayIterator;
use ArrayObject;

/**
 * @extends ArrayObject<int, Entity>
 */
class AbstractCollection extends ArrayObject
{
    /**
     * @param Entity $entity
     */
    public function add(Entity $entity): void
    {
        if ($entity->getId() === null) {
            $entity->setId($this->lastKey() + 1);
        }
        $this->offsetSet($entity->getId(), $entity);
        $this->asort();
    }

    /**
     * @return ArrayIterator<int, Entity>
     */
    public function list(): ArrayIterator
    {
        /** @var ArrayIterator<int, Entity> */
        return $this->getIterator();
    }

    public function remove(int $id): void
    {
        $this->offsetUnset($id);
    }

    private function lastKey(): int
    {
        return (int)array_key_last($this->getArrayCopy());
    }
}
