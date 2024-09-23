<?php

namespace App\Tests\Service;

use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Factory\FruitFactory;
use App\Factory\TypedTransferFactory;
use App\Factory\VegetableFactory;
use App\Service\StorageService;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    private string $request;
    private TypedTransferFactory $typedTransferFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = (string)file_get_contents('request.json');
        $this->typedTransferFactory = $this->createMock(TypedTransferFactory::class);
    }

    public function testReceivingRequest(): void
    {
        //Arrange
        $storageService = new StorageService($this->request, $this->typedTransferFactory);

        //Assert
        $this->assertNotEmpty($storageService->getRequest());
        $this->assertIsString($storageService->getRequest());
        $this->assertJson($storageService->getRequest());
    }

    public function testReceivingFruitCollection(): void
    {
        //Arrange
        $fruitFactory = new FruitFactory();
        $storageService = new StorageService($this->request, $fruitFactory);

        //Act
        $fruitsCollection = $storageService->toCollection();

        //Assert
        $this->assertInstanceOf(FruitCollection::class, $fruitsCollection);
        $this->assertEquals(10, $fruitsCollection->list()->count());
    }

    public function testReceivingVegetablesCollection(): void
    {
        //Arrange
        $vegetableFactory = new VegetableFactory();
        $storageService = new StorageService($this->request, $vegetableFactory);

        //Act
        $vegetableCollection = $storageService->toCollection();

        //Assert
        $this->assertInstanceOf(VegetableCollection::class, $vegetableCollection);
        $this->assertEquals(10, $vegetableCollection->list()->count());
    }
}
