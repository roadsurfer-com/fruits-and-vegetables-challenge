<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Fruit;
use App\Factory\FruitFactory;
use App\Repository\FruitRepository;
use App\Service\FruitsService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\LazyCriteriaCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FruitsServiceTest extends KernelTestCase
{
    /**
     * @var FruitRepository&\PHPUnit\Framework\MockObject\MockObject
     */
    private FruitRepository $fruitRepository;

    /**
     * @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private EntityManagerInterface $entityManager;
    private FruitsService $sut;

    protected function setUp(): void
    {
        $this->fruitRepository = $this->createMock(FruitRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->sut = new FruitsService(
            $this->fruitRepository,
            $this->entityManager
        );
    }

    public function testConvertWeightGivesCorrectUnit(): void
    {
        //Arrange
        $fruit = FruitFactory::createEntity(['id' => 1, 'name' => 'Banana', 'quantity' => 2000]);


        //Act
        $inGrams = $this->sut->convertWeight([$fruit], 'g');
        $inKilograms = $this->sut->convertWeight([$fruit], 'kg');
        $inTons = $this->sut->convertWeight([$fruit], 't');

        //Assert
        $this->assertEquals($inGrams, [$fruit]);
        $this->assertEquals($inKilograms, [$fruit->convertToKilogram()]);
        $this->assertEquals($inTons, [$fruit]);
    }

    public function testListAllFruits(): void
    {
        //Arrange
        $data = [new Fruit(), new Fruit(), new Fruit()];
        $lazyCriteriaCollection = $this->createMock(LazyCriteriaCollection::class);
        $lazyCriteriaCollection->expects($this->once())
            ->method('getValues')
            ->willReturn($data);

        $criteria = Criteria::create();
        $this->fruitRepository->expects($this->once())
            ->method('matching')
            ->with($criteria)
            ->willReturn($lazyCriteriaCollection);

        //Act
        $actual = $this->sut->list($criteria);

        //Assert
        $this->assertEquals($data, $actual);
    }

    public function testAddNewFruit(): void
    {
        //Arrange
        $fruit = FruitFactory::createEntity(['name' => 'Banana', 'quantity' => 2000]);

        //Assert
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($fruit);
        $this->entityManager->expects($this->once())
            ->method('flush');

        //Act
        $this->sut->add($fruit);
    }

    public function testRemoveFruit(): void
    {
        //Arrange
        $id = 1;
        $fruit = FruitFactory::createEntity(['id' => $id, 'name' => 'Banana', 'quantity' => 2000]);

        //Assert
        $this->fruitRepository->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($fruit);
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($fruit);
        $this->entityManager->expects($this->once())
            ->method('flush');

        //Act
        $this->sut->remove($id);
    }
}
