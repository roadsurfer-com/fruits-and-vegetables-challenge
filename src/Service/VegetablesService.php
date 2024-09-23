<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

final class VegetablesService
{
    public function __construct(
        private VegetableRepository $vegetableRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<int,Vegetable>
     */
    public function list(Criteria $criteria): array
    {
        return $this->vegetableRepository->matching($criteria)->getValues();
    }

    public function add(Vegetable $vegetable): void
    {
        //@todo - cover entity exists exception
        $this->entityManager->persist($vegetable);
        $this->entityManager->flush();
    }

    public function remove(int $id): void
    {
        $this->entityManager->remove($this->vegetableRepository->find($id));
        $this->entityManager->flush();
    }

    /**
     * @param array<int, Vegetable> $vegetables
     * @param string            $unit
     *
     * @return array<int, Vegetable>
     */
    public function convertWeight(array $vegetables, string $unit): array
    {
        foreach ($vegetables as $vegetable) {
            if ($unit === 'kg') {
                $vegetable->convertToKilogram();
            }
        }

        return $vegetables;
    }
}
