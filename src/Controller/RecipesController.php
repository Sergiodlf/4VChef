<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\RecipeNutrient;
use App\Entity\Step;
use App\Repository\NutrientTypeRepository;
use App\Repository\RecipeRepository;
use App\Repository\RecipeTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recipes')]
class RecipesController extends AbstractController
{
    /**
     * GET /recipes
     * GET /recipes?typeId=1
     */
    #[Route('', methods: ['GET'])]
    public function list(Request $request, RecipeRepository $repository): JsonResponse
    {
        $typeId = $request->query->get('typeId');

        if ($typeId) {
            $recipes = $repository->findBy([
                'type' => $typeId,
                'isDeleted' => false
            ]);
        } else {
            $recipes = $repository->findBy([
                'isDeleted' => false
            ]);
        }

        $data = [];

        foreach ($recipes as $recipe) {
            $data[] = [
                'id' => $recipe->getId(),
                'title' => $recipe->getTitle(),
                'diners' => $recipe->getDiners(),
                'type' => [
                    'id' => $recipe->getType()->getId(),
                    'name' => $recipe->getType()->getName()
                ],
            ];
        }

        return $this->json($data);
    }

    /**
     * POST /recipes
     */
    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        RecipeTypeRepository $recipeTypeRepository,
        NutrientTypeRepository $nutrientTypeRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = $request->toArray();

        // Validaciones básicas
        if (
            empty($data['title']) ||
            empty($data['diners']) ||
            empty($data['typeId'])
        ) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        if (empty($data['ingredients']) || count($data['ingredients']) < 1) {
            return $this->json(['error' => 'At least one ingredient is required'], 400);
        }

        if (empty($data['steps']) || count($data['steps']) < 1) {
            return $this->json(['error' => 'At least one step is required'], 400);
        }

        // Tipo de receta
        $type = $recipeTypeRepository->find($data['typeId']);
        if (!$type) {
            return $this->json(['error' => 'Recipe type not found'], 404);
        }

        // Crear receta
        $recipe = new Recipe();
        $recipe->setTitle($data['title']);
        $recipe->setDiners($data['diners']);
        $recipe->setType($type);

        // Ingredientes
        foreach ($data['ingredients'] as $ingredientData) {
            if (
                empty($ingredientData['name']) ||
                empty($ingredientData['quantity']) ||
                empty($ingredientData['unit'])
            ) {
                return $this->json(['error' => 'Invalid ingredient data'], 400);
            }

            $ingredient = new Ingredient();
            $ingredient->setName($ingredientData['name']);
            $ingredient->setQuantity($ingredientData['quantity']);
            $ingredient->setUnit($ingredientData['unit']);

            $recipe->addIngredient($ingredient);
        }

        // Pasos
        foreach ($data['steps'] as $stepData) {
            if (
                empty($stepData['order']) ||
                empty($stepData['description'])
            ) {
                return $this->json(['error' => 'Invalid step data'], 400);
            }

            $step = new Step();
            $step->setStepOrder($stepData['order']);
            $step->setDescription($stepData['description']);

            $recipe->addStep($step);
        }

        // Nutrientes (opcional)
        if (!empty($data['nutritionalValues'])) {
            foreach ($data['nutritionalValues'] as $nutrientData) {
                if (
                    empty($nutrientData['nutrientTypeId']) ||
                    empty($nutrientData['quantity'])
                ) {
                    return $this->json(['error' => 'Invalid nutrient data'], 400);
                }

                $nutrientType = $nutrientTypeRepository->find($nutrientData['nutrientTypeId']);
                if (!$nutrientType) {
                    return $this->json(['error' => 'Nutrient type not found'], 404);
                }

                $recipeNutrient = new RecipeNutrient();
                $recipeNutrient->setNutrientType($nutrientType);
                $recipeNutrient->setQuantity($nutrientData['quantity']);

                $recipe->addNutritionalValue($recipeNutrient);
            }
        }

        $em->persist($recipe);
        $em->flush();

        return $this->json([
            'id' => $recipe->getId(),
            'message' => 'Recipe created'
        ], 201);
    }

    /**
     * DELETE /recipes/{id} (borrado lógico)
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        int $id,
        RecipeRepository $repository,
        EntityManagerInterface $em
    ): JsonResponse {
        $recipe = $repository->find($id);

        if (!$recipe || $recipe->isDeleted()) {
            return $this->json(['error' => 'Recipe not found'], 404);
        }

        $recipe->setIsDeleted(true);
        $em->flush();

        return $this->json(null, 204);
    }

    /**
     * POST /recipes/{id}/ratings
     */
    #[Route('/{id}/ratings', methods: ['POST'])]
    public function rate(
        int $id,
        Request $request,
        RecipeRepository $recipeRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $recipe = $recipeRepository->find($id);

        if (!$recipe || $recipe->isDeleted()) {
            return $this->json(['error' => 'Recipe not found'], 404);
        }

        $data = $request->toArray();

        if (!isset($data['score']) || $data['score'] < 0 || $data['score'] > 5) {
            return $this->json(['error' => 'Score must be between 0 and 5'], 400);
        }

        $ip = $request->getClientIp();

        $rating = new Rating();
        $rating->setScore($data['score']);
        $rating->setComment($data['comment'] ?? null);
        $rating->setIpAddress($ip);
        $rating->setRecipe($recipe);

        try {
            $em->persist($rating);
            $em->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            return $this->json(['error' => 'You already voted for this recipe'], 409);
        }

        return $this->json(['message' => 'Vote registered'], 201);
    }
}
