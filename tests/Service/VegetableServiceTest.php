<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Vegetable;
use App\Factory\VegetableFactory;
use App\Repository\VegetableRepository;
use App\Service\VegetablesService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\LazyCriteriaCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VegetableServiceTest extends KernelTestCase
{
    /**
     * @var VegetableRepository&\PHPUnit\Framework\MockObject\MockObject
     */
    private VegetableRepository $vegetableRepository;

    /**
     * @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private EntityManagerInterface $entityManager;
    private VegetablesService $sut;

    protected function setUp(): void
    {
        $this->vegetableRepository = $this->createMock(VegetableRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->sut = new VegetablesService(
            $this->vegetableRepository,
            $this->entityManager
        );
    }

    public function testConvertWeightGivesCorrectUnit(): void
    {
        //Arrange
        $vegetable = VegetableFactory::createEntity(['id' => 1, 'name' => 'Banana', 'quantity' => 2000]);


        //Act
        $inGrams = $this->sut->convertWeight([$vegetable], 'g');
        $inKilograms = $this->sut->convertWeight([$vegetable], 'kg');
        $inTons = $this->sut->convertWeight([$vegetable], 't');

        //Assert
        $this->assertEquals($inGrams, [$vegetable]);
        $this->assertEquals($inKilograms, [$vegetable->convertToKilogram()]);
        $this->assertEquals($inTons, [$vegetable]);
    }

    public function testListAllVegetables(): void
    {
        //Arrange
        $data = [new Vegetable(), new Vegetable(), new Vegetable()];
        $lazyCriteriaCollection = $this->createMock(LazyCriteriaCollection::class);
        $lazyCriteriaCollection->expects($this->once())
            ->method('getValues')
            ->willReturn($data);

        $criteria = Criteria::create();
        $this->vegetableRepository->expects($this->once())
            ->method('matching')
            ->with($criteria)
            ->willReturn($lazyCriteriaCollection);

        //Act
        $actual = $this->sut->list($criteria);

        //Assert
        $this->assertEquals($data, $actual);
    }

    public function testAddNewVegetable(): void
    {
        //Arrange
        $vegetable = VegetableFactory::createEntity(['name' => 'Banana', 'quantity' => 2000]);

        //Assert
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($vegetable);
        $this->entityManager->expects($this->once())
            ->method('flush');

        //Act
        $this->sut->add($vegetable);
    }

    public function testRemoveVegetable(): void
    {
        //Arrange
        $id = 1;
        $vegetable = VegetableFactory::createEntity(['id' => $id, 'name' => 'Banana', 'quantity' => 2000]);

        //Assert
        $this->vegetableRepository->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($vegetable);
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($vegetable);
        $this->entityManager->expects($this->once())
            ->method('flush');

        //Act
        $this->sut->remove($id);
    }
}
