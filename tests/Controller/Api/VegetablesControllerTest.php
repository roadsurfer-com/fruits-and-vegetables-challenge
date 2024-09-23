<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Repository\VegetableRepository;
use App\Tests\FixturesLoaderTrait;
use App\Tests\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class VegetablesControllerTest extends WebTestCase
{
    use FixturesLoaderTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadVegetableFixtures();
    }

    /**
     * @param array<string, mixed> $filters
     */
    #[DataProvider('searchPhrasesDataProvider')]
    public function testCanSearchByVegetableName(string $phrase, array $filters, int $expectedCount): void
    {
        //Act
        $crawler = $this->client->request('GET', '/api/vegetables/search/' . $phrase, $filters);

        //Assert
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();
        /** @var array<int, mixed> $data */
        $data = json_decode($content, true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertCount($expectedCount, $data);
    }

    /**
     * @return array<string,array<string, mixed>>
     */
    public static function searchPhrasesDataProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength
        return [
            '`er` phrase'                      => ['phrase' => 'er', 'filters' => [], 'expectedCount' => 3],
            '`er` phrase + min_quantity'       => ['phrase' => 'er', 'filters' => ['min_quantity' => 10000000], 'expectedCount' => 0],
            '`er` phrase + min_quantity in g'  => ['phrase' => 'er', 'filters' => ['min_quantity' => 1000], 'expectedCount' => 3],
            '`er` phrase + min_quantity in kg' => ['phrase' => 'er', 'filters' => ['min_quantity' => 1000, 'unit' => 'kg'], 'expectedCount' => 0],
            '`carr` phrase'                    => ['phrase' => 'carr', 'filters' => [], 'expectedCount' => 1],
            '`tomato` phrase'                  => ['phrase' => 'IdontExists', 'filters' => [], 'expectedCount' => 0],
        ];
        // phpcs:enable
    }

    public function testCanRemoveExistingVegetable(): void
    {
        //Arrange
        $id = 1;
        /** @var VegetableRepository $repository */
        $repository = $this->getContainer()->get(VegetableRepository::class);
        $repository->find($id);

        //Act
        $this->client->request(
            'DELETE',
            '/api/vegetables/' . $id,
        );

        //Assert
        $this->assertResponseIsSuccessful();
        $this->assertNull($repository->find($id));
    }

    public function testCanAddNewVegetable(): void
    {
        //Arrange
        $vegetableData = ['name' => 'Tomatoes', 'quantity' => 1000];

        //Act
        $this->client->request(
            'POST',
            '/api/vegetables',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string)json_encode($vegetableData)
        );

        //Assert
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();
        /** @var array<int, mixed> $data */
        $data = json_decode($content, true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertNotEmpty($data);
        $this->assertEquals(['id' => 11, 'unit' => 'g'] + $vegetableData, $data);
    }

    public function testCannotAddNewVegetableWithWrongRequestData(): void
    {
        //Arrange
        $fruitData = ['name' => 'Tomatoes', 'quantity' => -1000.0];

        //Act
        $this->client->request(
            'POST',
            '/api/fruits',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string)json_encode($fruitData)
        );

        //Assert
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('This value should be positive.', $content);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('filtersDataProvider')]
    public function testCanGetVegetablesList(array $params, int $expectedCount): void
    {
        //Act
        $crawler = $this->client->request('GET', '/api/vegetables', $params);

        //Assert
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();
        /** @var array<int, mixed> $data */
        $data = json_decode($content, true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertCount($expectedCount, $data);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function filtersDataProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength
        return [
            'No filters'                              => ['params' => [], 'expectedCount' => 10],
            'Filter by id'                            => ['params' => ['id' => 1], 'expectedCount' => 1],
            'Filter by name'                          => ['params' => ['name' => 'Carrot'], 'expectedCount' => 1],
            'Filter by min_quantity'                  => ['params' => ['min_quantity' => 25000], 'expectedCount' => 4],
            'Filter by min_quantity in kg'            => ['params' => ['min_quantity' => 25, 'unit' => 'kg'], 'expectedCount' => 4],
            'Filter by max_quantity'                  => ['params' => ['max_quantity' => 25000], 'expectedCount' => 6],
            'Filter by max_quantity in kg'            => ['params' => ['max_quantity' => 25, 'unit' => 'kg'], 'expectedCount' => 6],
            'Filter by min_quantity and max_quantity' => ['params' => ['min_quantity' => 20000, 'max_quantity' => 21000], 'expectedCount' => 1],
        ];
        // phpcs:enable
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('unitConverterDataProvider')]
    public function testCanGetSingleVegetable(array $params, float $expectedQuantity): void
    {
        //Act
        $crawler = $this->client->request('GET', '/api/vegetables/1', $params);
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();
        /** @var array<int, mixed> $data */
        $data = json_decode($content, true);

        //Assert
        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertEquals(
            ['id' => 1, 'name' => 'Carrot', 'quantity' => $expectedQuantity, 'unit' => $params['unit'] ?? 'g'],
            $data
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function unitConverterDataProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength
        return [
            'Carrot in g but without unit param' => ['params' => ['id' => 1,], 'expectedQuantity' => 10922],
            'Carrot in g'                        => ['params' => ['id' => 1, 'unit' => 'g'], 'expectedQuantity' => 10922],
            'Carrot in kg'                       => ['params' => ['id' => 1, 'unit' => 'kg'], 'expectedQuantity' => 10.922],
        ];
        // phpcs:enable
    }
}
