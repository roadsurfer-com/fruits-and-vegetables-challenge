<?php

namespace App\Tests\Collection;

use App\Collection\FruitCollection;
use App\Factory\FruitFactory;
use PHPUnit\Framework\TestCase;

class FruitCollectionTest extends TestCase
{
    private FruitCollection $sut;

    protected function setUp(): void
    {
        $this->sut = FruitFactory::createCollection();
    }

    public function testFruitCollectionIsListable(): void
    {
        //Arrange
        $fruits = [
            FruitFactory::createEntity(['id' => 1, 'name' => 'Tomato', 'quantity' => 1]),
            FruitFactory::createEntity(['name' => 'Apple', 'quantity' => 100]),
        ];

        foreach ($fruits as $fruit) {
            $this->sut->add($fruit);
        }

        //Act
        $actualFruitList = $this->sut->list();

        //Assert
        foreach ($fruits as $fruit) {
            $this->assertContains($fruit, $actualFruitList);
        }
    }

    public function testFruitAddedToFruitCollection(): void
    {
        //Arrange
        $fruit = FruitFactory::createEntity(['id' => 1, 'name' => 'Tomato', 'quantity' => 1]);

        //Act
        $this->sut->add($fruit);

        //Assert
        $this->assertEquals(1, $this->sut->list()->count());
        $this->assertcontains($fruit, $this->sut->list());
    }

    public function testFruitRemovedFromCollection(): void
    {
        //Arrange
        $fruits = [
            FruitFactory::createEntity(['id' => 1, 'name' => 'Tomato', 'quantity' => 1]),
            FruitFactory::createEntity(['id' => 2, 'name' => 'Apple', 'quantity' => 100]),
        ];

        foreach ($fruits as $fruit) {
            $this->sut->add($fruit);
        }

        //Act
        $this->sut->remove((int)$fruits[0]->getId());

        //Assert
        $this->assertEquals(count($fruits) - 1, $this->sut->list()->count());
        $this->assertNotContains($fruits[0], $this->sut->list());
        $this->assertContains($fruits[1], $this->sut->list());
    }
}
