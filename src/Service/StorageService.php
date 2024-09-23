<?php

declare(strict_types=1);

namespace App\Service;

use App\Collection\Collection;
use App\Factory\TypedTransferFactory;

final class StorageService implements TypedTransferAwareStorageService
{
    public function __construct(
        private string $request,
        private TypedTransferFactory $typedTransferFactory,
    ) {
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function toCollection(): Collection
    {
        $collection = $this->typedTransferFactory::createCollection();

        foreach ($this->getDataByType($collection::TYPE) as $rawData) {
            $entity = $this->typedTransferFactory::createEntity($this->convertToGrams($rawData));
            $collection->add($entity);
        }

        return $collection;
    }


    /**
     * @param array<string, string|float|int> $rawEntityData
     *
     * @return array<string, string|float|int>
     */
    private function convertToGrams(array $rawEntityData): array
    {
        if ($rawEntityData['unit'] === 'kg') {
            $rawEntityData['quantity'] = (float)$rawEntityData['quantity'] * 1000;
        }

        return $rawEntityData;
    }

    /**
     * @return array<int, array<string, string|float|int>>
     */
    private function getDataByType(string $type): array
    {
        /** @var array<int, array<string, string|float|int>> $data */
        $data = json_decode($this->request, true);

        return array_filter(
            $data,
            fn(array $fruitData): bool => $fruitData['type'] === $type
        );
    }
}
