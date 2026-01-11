<?php

namespace App\Controller;

use App\Repository\NutrientTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nutrients')]
class NutrientTypesController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(NutrientTypeRepository $repository): JsonResponse
    {
        $nutrients = $repository->findAll();

        $data = [];
        foreach ($nutrients as $nutrient) {
            $data[] = [
                'id' => $nutrient->getId(),
                'code' => $nutrient->getCode(),
                'name' => $nutrient->getName(),
                'unit' => $nutrient->getUnit(),
            ];
        }

        return $this->json($data);
    }
}
