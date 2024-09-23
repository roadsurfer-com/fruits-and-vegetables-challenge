<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

final class FruitsService
{
    public function __construct(
        private FruitRepository $fruitRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<int,Fruit>
     */
    public function list(Criteria $criteria): array
    {
        return $this->fruitRepository->matching($criteria)->getValues();
    }

    public function add(Fruit $fruit): void
    {
        //@todo - cover entity exists exception
        $this->entityManager->persist($fruit);
        $this->entityManager->flush();
    }

    public function remove(int $id): void
    {
        $this->entityManager->remove($this->fruitRepository->find($id));
        $this->entityManager->flush();
    }

    /**
     * @param array<int, Fruit> $fruits
     * @param string            $unit
     *
     * @return array<int, Fruit>
     */
    public function convertWeight(array $fruits, string $unit): array
    {
        foreach ($fruits as $fruit) {
            if ($unit === 'kg') {
                $fruit->convertToKilogram();
            }
        }

        return $fruits;
    }
}
