<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Collection\Collection;
use App\Collection\VegetableCollection;
use App\Factory\VegetableFactory;
use App\Factory\TypedTransferFactory;
use App\Service\StorageService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class VegetableFixtures extends Fixture
{
    private const REQUEST_FILE_NAME = 'request.json';

    public function __construct(
        #[Autowire(service: VegetableFactory::class)]
        private TypedTransferFactory $vegetableFactory,
        private string $projectDirectory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->loadVegetablesCollection() as $vegetable) {
            $manager->persist($vegetable);
        }

        $manager->flush();
    }

    private function loadVegetablesCollection(): VegetableCollection
    {
        $storageService = new StorageService($this->getRequest(), $this->vegetableFactory);
        /** @var VegetableCollection&Collection $vegetableCollection */
        $vegetableCollection = $storageService->toCollection();

        return $vegetableCollection;
    }

    private function getRequest(): string
    {
        return (string)file_get_contents($this->projectDirectory . self::REQUEST_FILE_NAME);
    }
}
