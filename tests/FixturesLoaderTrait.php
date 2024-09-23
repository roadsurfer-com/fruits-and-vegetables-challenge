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
        /** @var AbstractDatabaseTool $databaseTool */
        $databaseTool = $this->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            FruitFixtures::class
        ]);
    }

    private function loadVegetableFixtures(): void
    {
        /** @var AbstractDatabaseTool $databaseTool */
        $databaseTool = $this->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            VegetableFixtures::class
        ]);
    }
}
