<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Constraints as Assert;

trait ValidatorTrait
{
    private function validateRequest(array $fruitData): void
    {
        $constraints = new Assert\Collection([
            'id'            => [new Assert\Optional([new Assert\Positive()])],
            'search_phrase' => [new Assert\Optional([
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 255])
            ])],
            'name'          => [new Assert\Optional([
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 255])
            ])],
            'quantity'      => [new Assert\Optional([new Assert\Positive()])],
            'min_quantity'  => [new Assert\Optional([new Assert\Positive()])],
            'max_quantity'  => [new Assert\Optional([new Assert\Positive()])],
            'unit'          => [new Assert\Optional([new Assert\Choice(['g', 'kg'])])]
        ]);

        $errors = $this->validator->validate($fruitData, $constraints);

        if ($errors->count()) {
            throw new BadRequestException($errors->offsetGet(0)->getMessage());
        }
    }
}
