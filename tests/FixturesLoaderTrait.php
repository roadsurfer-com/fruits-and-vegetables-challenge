<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\FruitFixtures;
use App\DataFixtures\VegetableFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

trait FixturesLoaderTrait
{
    private function loadFruitFixtures(): void
    {
        $databaseTool = $this->getDatabaseToolCollection()->get();
        $databaseTool->loadFixtures([
            FruitFixtures::class
        ]);
    }

    private function loadVegetableFixtures(): void
    {
        $databaseTool = $this->getDatabaseToolCollection()->get();
        $databaseTool->loadFixtures([
            VegetableFixtures::class
        ]);
    }

    private function getDatabaseToolCollection(): DatabaseToolCollection
    {
        /** @var DatabaseToolCollection */
        return $this->getContainer()->get(DatabaseToolCollection::class);
    }
}
