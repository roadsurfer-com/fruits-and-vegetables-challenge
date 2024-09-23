<?php

namespace App\Controller\Api;

use App\Factory\CriteriaFactory;
use App\Factory\VegetableFactory;
use App\Service\VegetablesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class VegetablesController extends AbstractController
{
    public function __construct(private VegetablesService $vegetablesService)
    {
    }

    #[Route('/api/vegetables/{id}', name: 'app_api_vegetable', methods: ['GET'])]
    public function index(int $id, Request $request): JsonResponse
    {
        $criteria = CriteriaFactory::create(['id' => $id] + $request->query->all());

        $list = $this->vegetablesService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->vegetablesService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json(reset($list));
    }

    #[Route('/api/vegetables', name: 'app_api_vegetables', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $criteria = CriteriaFactory::create($request->query->all());

        $list = $this->vegetablesService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->vegetablesService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json($list);
    }

    #[Route('/api/vegetables', name: 'app_api_vegetable_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        //@todo - add request validation

        $vegetableData = json_decode($request->getContent(), true);
        $vegetable = VegetableFactory::createEntity($vegetableData);

        $this->vegetablesService->add($vegetable);

        return $this->json($vegetable);
    }

    #[Route('/api/vegetables/{id}', name: 'app_api_vegetable_delete', methods: ['DELETE'])]
    public function remove(int $id): JsonResponse
    {
        $this->vegetablesService->remove($id);

        return $this->json([]);
    }

    #[Route('/api/vegetables/search/{phrase}', name: 'app_api_vegetables_search_by_name', methods: ['GET'])]
    public function search(string $phrase, Request $request): JsonResponse
    {
        //@todo - validation
        $criteria = CriteriaFactory::create(['search_phrase' => $phrase] + $request->query->all());

        $list = $this->vegetablesService->list($criteria);

        if ($request->query->get('unit')) {
            $list = $this->vegetablesService->convertWeight($list, $request->query->get('unit'));
        }

        return $this->json($list);
    }
}
