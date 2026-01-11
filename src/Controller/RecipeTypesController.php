<?php

namespace App\Controller;

use App\Repository\RecipeTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recipe-types')]
class RecipeTypesController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(RecipeTypeRepository $repository): JsonResponse
    {
        $types = $repository->findAll();

        $data = [];
        foreach ($types as $type) {
            $data[] = [
                'id' => $type->getId(),
                'code' => $type->getCode(),
                'name' => $type->getName(),
                'description' => $type->getDescription(),
            ];
        }

        return $this->json($data);
    }
}
