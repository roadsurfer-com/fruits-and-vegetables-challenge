<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Repository\FruitRepository;
use App\Tests\FixturesLoaderTrait;
use App\Tests\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FruitsControllerTest extends WebTestCase
{
    use FixturesLoaderTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFruitFixtures();
    }

    /**
     * @param array<string, mixed> $filters
     */
    #[DataProvider('searchPhrasesDataProvider')]
    public function testCanSearchByFruitName(string $phrase, array $filters, int $expectedCount): void
    {
        //Act
        $crawler = $this->client->request('GET', '/api/fruits/search/' . $phrase, $filters);

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
            '`es` phrase'                      => ['phrase' => 'es', 'filters' => [], 'expectedCount' => 3],
            '`es` phrase + min_quantity'       => ['phrase' => 'es', 'filters' => ['min_quantity' => 10000000], 'expectedCount' => 0],
            '`es` phrase + min_quantity in g'  => ['phrase' => 'es', 'filters' => ['min_quantity' => 1000], 'expectedCount' => 3],
            '`es` phrase + min_quantity in kg' => ['phrase' => 'es', 'filters' => ['min_quantity' => 1000, 'unit' => 'kg'], 'expectedCount' => 0],
            '`apple` phrase'                   => ['phrase' => 'apple', 'filters' => [], 'expectedCount' => 1],
            '`tomato` phrase'                  => ['phrase' => 'tomato', 'filters' => [], 'expectedCount' => 0],
        ];
        // phpcs:enable
    }

    public function testCanRemoveExistingFruit(): void
    {
        //Arrange
        $id = 1;
        /** @var FruitRepository $repository */
        $repository = $this->getContainer()->get(FruitRepository::class);
        $repository->find($id);

        //Act
        $this->client->request(
            'DELETE',
            '/api/fruits/' . $id,
        );

        //Assert
        $this->assertResponseIsSuccessful();
        $this->assertNull($repository->find($id));
    }

    public function testCanAddNewFruit(): void
    {
        //Arrange
        $fruitData = ['name' => 'Orange', 'quantity' => 1000.0];

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
        /** @var array<int, mixed> $data */
        $data = json_decode($content, true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertNotEmpty($data);
        $this->assertEquals(['id' => 11, 'unit' => 'g'] + $fruitData, $data);
    }

    public function testCannotAddNewFruitWithWrongRequestData(): void
    {
        //Arrange
        $fruitData = ['name' => 'Orange', 'quantity' => -1000.0];

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
    public function testCanGetFruitsList(array $params, int $expectedCount): void
    {
        //Act
        $crawler = $this->client->request('GET', '/api/fruits', $params);

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
            'Filter by name'                          => ['params' => ['name' => 'Apples'], 'expectedCount' => 1],
            'Filter by min_quantity'                  => ['params' => ['min_quantity' => 25000], 'expectedCount' => 3],
            'Filter by min_quantity in kg'            => ['params' => ['min_quantity' => 25, 'unit' => 'kg'], 'expectedCount' => 3],
            'Filter by max_quantity'                  => ['params' => ['max_quantity' => 25000], 'expectedCount' => 7],
            'Filter by max_quantity in kg'            => ['params' => ['max_quantity' => 25, 'unit' => 'kg'], 'expectedCount' => 7],
            'Filter by min_quantity and max_quantity' => ['params' => ['min_quantity' => 20000, 'max_quantity' => 21000], 'expectedCount' => 2],
        ];
        // phpcs:enable
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('unitConverterDataProvider')]
    public function testCanGetSingleFruit(array $params, int $expectedQuantity): void
    {
        //Act
        $crawler = $this->client->request('GET', '/api/fruits/1', $params);
        /** @var string $content */
        $content = $this->client->getResponse()->getContent();
        /** @var array<int, mixed> $data */
        $data = json_decode($content, true);

        //Assert
        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertEquals(
            ['id' => 1, 'name' => 'Apples', 'quantity' => $expectedQuantity, 'unit' => $params['unit'] ?? 'g'],
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
            'Apples in g but without unit param' => ['params' => ['id' => 1,], 'expectedQuantity' => 20000],
            'Apples in g'                        => ['params' => ['id' => 1, 'unit' => 'g'], 'expectedQuantity' => 20000],
            'Apples in kg'                       => ['params' => ['id' => 1, 'unit' => 'kg'], 'expectedQuantity' => 20],
        ];
        // phpcs:enable
    }
}
