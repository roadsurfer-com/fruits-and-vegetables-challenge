<?php

namespace App\Tests\Collection;

use App\Collection\VegetableCollection;
use App\Factory\VegetableFactory;
use PHPUnit\Framework\TestCase;

class VegetablesCollectionTest extends TestCase
{
    private VegetableCollection $sut;

    protected function setUp(): void
    {
        $this->sut = VegetableFactory::createCollection();
    }

    public function testVegetableCollectionIsListable(): void
    {
        //Arrange
        $vegetables = [
            VegetableFactory::createEntity(['name' => 'Onion', 'quantity' => 1]),
            VegetableFactory::createEntity(['id' => 2, 'name' => 'Cucumbers', 'quantity' => 100]),
        ];

        foreach ($vegetables as $vegetable) {
            $this->sut->add($vegetable);
        }

        //Act
        $actualVegetableList = $this->sut->list();

        //Assert
        foreach ($vegetables as $vegetable) {
            $this->assertContains($vegetable, $actualVegetableList);
        }
    }

    public function testVegetableAddedToVegetableCollection(): void
    {
        //Arrange
        $vegetable = VegetableFactory::createEntity(['id' => 1, 'name' => 'Onion', 'quantity' => 1]);

        //Act
        $this->sut->add($vegetable);

        //Assert
        $this->assertEquals(1, $this->sut->list()->count());
        $this->assertcontains($vegetable, $this->sut->list());
    }

    public function testVegetableRemovedFromCollection(): void
    {
        //Arrange
        $vegetables = [
            VegetableFactory::createEntity(['id' => 1, 'name' => 'Onion', 'quantity' => 1]),
            VegetableFactory::createEntity(['id' => 2, 'name' => 'Carrot', 'quantity' => 100]),
        ];

        foreach ($vegetables as $vegetable) {
            $this->sut->add($vegetable);
        }

        //Act
        $this->sut->remove($vegetables[0]->getId());

        //Assert
        $this->assertEquals(count($vegetables) - 1, $this->sut->list()->count());
        $this->assertNotContains($vegetables[0], $this->sut->list());
        $this->assertContains($vegetables[1], $this->sut->list());
    }
}
