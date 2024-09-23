<?php

declare(strict_types=1);

namespace App\Factory;

use Doctrine\Common\Collections\Criteria;

final class CriteriaFactory
{
    /**
     * @param array<string, float|int|string> $filters
     */
    public static function create(array $filters): Criteria
    {
        $criteria = Criteria::create();

        $weightMultiplier = 1;
        if (isset($filters['unit']) && $filters['unit'] === 'kg') {
            $weightMultiplier = 1000;
        }

        if (isset($filters['id'])) {
            $criteria = $criteria->andWhere(Criteria::expr()->eq('id', $filters['id']));
        }

        if (isset($filters['name'])) {
            $criteria = $criteria->andWhere(Criteria::expr()->eq('name', $filters['name']));
        }

        if (isset($filters['min_quantity']) && is_numeric($filters['min_quantity'])) {
            $filters['min_quantity'] *= $weightMultiplier;
            $criteria = $criteria->andWhere(Criteria::expr()->gte('quantity', $filters['min_quantity']));
        }

        if (isset($filters['max_quantity']) && is_numeric($filters['max_quantity'])) {
            $filters['max_quantity'] *= $weightMultiplier;
            $criteria = $criteria->andWhere(Criteria::expr()->lte('quantity', $filters['max_quantity']));
        }

        if (isset($filters['search_phrase'])) {
            $criteria = $criteria->andWhere(Criteria::expr()->contains('name', $filters['search_phrase']));
        }

        return $criteria;
    }
}
