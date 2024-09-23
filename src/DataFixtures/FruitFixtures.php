<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Collection\Collection;
use App\Collection\FruitCollection;
use App\Factory\FruitFactory;
use App\Factory\TypedTransferFactory;
use App\Service\StorageService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class FruitFixtures extends Fixture
{
    private const REQUEST_FILE_NAME = 'request.json';

    public function __construct(
        #[Autowire(service: FruitFactory::class)]
        private TypedTransferFactory $fruitFactory,
        private string $projectDirectory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->loadFruitsCollection() as $fruit) {
            $manager->persist($fruit);
        }

        $manager->flush();
    }

    private function loadFruitsCollection(): FruitCollection
    {
        $storageService = new StorageService($this->getRequest(), $this->fruitFactory);
        /** @var FruitCollection&Collection $fruitCollection */
        $fruitCollection = $storageService->toCollection();

        return $fruitCollection;
    }

    private function getRequest(): string
    {
        return (string)file_get_contents($this->projectDirectory . self::REQUEST_FILE_NAME);
    }
}
