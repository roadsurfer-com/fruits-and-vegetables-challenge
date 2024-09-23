<?php

namespace App\Controller\Api;

use App\Factory\CriteriaFactory;
use App\Factory\FruitFactory;
use App\Service\FruitsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FruitsController extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private FruitsService $fruitsService,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/fruits/{id}', name: 'app_api_fruit', methods: ['GET'])]
    public function index(int $id, Request $request): JsonResponse
    {
        $this->validateRequest(['id' => $id] + $request->query->all());
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
        $this->validateRequest($request->query->all());
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
        $fruitData = json_decode($request->getContent(), true);
        $this->validateRequest($fruitData);

        $fruit = FruitFactory::createEntity($fruitData);

        $this->fruitsService->add($fruit);

        return $this->json($fruit);
    }

    #[Route('/api/fruits/{id}', name: 'app_api_fruit_delete', methods: ['DELETE'])]
    public function remove(int $id): JsonResponse
    {
        $this->validateRequest(['id' => $id]);
        $this->fruitsService->remove($id);

        return $this->json([]);
    }

    #[Route('/api/fruits/search/{phrase}', name: 'app_api_fruits_search_by_name', methods: ['GET'])]
    public function search(string $phrase, Request $request): JsonResponse
    {
        $this->validateRequest(['search_phrase' => $phrase] + $request->query->all());

        $criteria = CriteriaFactory::create(['search_phrase' => $phrase] + $request->query->all());
        $list = $this->fruitsService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->fruitsService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json($list);
    }
}
