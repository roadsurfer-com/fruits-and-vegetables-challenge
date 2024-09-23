<?php

namespace App\Controller\Api;

use App\Factory\CriteriaFactory;
use App\Factory\FruitFactory;
use App\Service\FruitsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class FruitsController extends AbstractController
{
    public function __construct(private FruitsService $fruitsService)
    {
    }

    #[Route('/api/fruits/{id}', name: 'app_api_fruit', methods: ['GET'])]
    public function index(int $id, Request $request): JsonResponse
    {
        $criteria = CriteriaFactory::create(['id' => $id] + $request->query->all());

        $list = $this->fruitsService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->fruitsService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json(reset($list));
    }

    #[Route('/api/fruits', name: 'app_api_fruits', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $criteria = CriteriaFactory::create($request->query->all());

        $list = $this->fruitsService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->fruitsService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json($list);
    }

    #[Route('/api/fruits', name: 'app_api_fruit_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        //@todo - add request validation

        $fruitData = json_decode($request->getContent(), true);
        $fruit = FruitFactory::createEntity($fruitData);

        $this->fruitsService->add($fruit);

        return $this->json($fruit);
    }

    #[Route('/api/fruits/{id}', name: 'app_api_fruit_delete', methods: ['DELETE'])]
    public function remove(int $id): JsonResponse
    {
        $this->fruitsService->remove($id);

        return $this->json([]);
    }

    #[Route('/api/fruits/search/{phrase}', name: 'app_api_fruits_search_by_name', methods: ['GET'])]
    public function search(string $phrase, Request $request): JsonResponse
    {
        //@todo - validation
        $criteria = CriteriaFactory::create(['search_phrase' => $phrase] + $request->query->all());

        $list = $this->fruitsService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->fruitsService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json($list);
    }
}
