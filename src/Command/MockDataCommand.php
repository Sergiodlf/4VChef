<?php

namespace App\Command;

use App\Entity\NutrientType;
use App\Entity\RecipeType;
use App\Repository\NutrientTypeRepository;
use App\Repository\RecipeTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-mock-data',
    description: 'Populates the DB with mock recipe types and nutrient types'
)]
class MockDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RecipeTypeRepository $recipeTypeRepository,
        private NutrientTypeRepository $nutrientTypeRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // --------------------
        // Recipe Types (catálogo)
        // --------------------
        $recipeTypes = [
            ['POSTRE', 'Postre', 'Recetas dulces para el final de la comida'],
            ['ENSALADA', 'Ensalada', 'Recetas frías y ligeras'],
            ['CARNE', 'Carnes', 'Recetas con carne como ingrediente principal'],
            ['POTAJE', 'Potajes', 'Platos de cuchara tradicionales'],
            ['PASTA', 'Pasta', 'Recetas basadas en pasta'],
            ['PESCADO', 'Pescado', 'Recetas con pescado o marisco'],
        ];

        $createdRecipeTypes = 0;

        foreach ($recipeTypes as [$code, $name, $description]) {
            // Evitar duplicados (idempotente)
            $existing = $this->recipeTypeRepository->findOneBy(['code' => $code]);
            if ($existing) {
                continue;
            }

            $type = new RecipeType();
            $type->setCode($code);
            $type->setName($name);
            $type->setDescription($description);

            $this->entityManager->persist($type);
            $createdRecipeTypes++;
        }

        // --------------------
        // Nutrient Types (catálogo)
        // --------------------
        $nutrientTypes = [
            ['KCAL', 'Calorías', 'kcal'],
            ['PROT', 'Proteínas', 'g'],
            ['HC', 'Hidratos de carbono', 'g'],
            ['GRAS', 'Grasas', 'g'],
            ['FIB', 'Fibra', 'g'],
            ['SOD', 'Sodio', 'mg'],
        ];

        $createdNutrientTypes = 0;

        foreach ($nutrientTypes as [$code, $name, $unit]) {
            // Evitar duplicados (idempotente)
            $existing = $this->nutrientTypeRepository->findOneBy(['code' => $code]);
            if ($existing) {
                continue;
            }

            $nutrient = new NutrientType();
            $nutrient->setCode($code);
            $nutrient->setName($name);
            $nutrient->setUnit($unit);

            $this->entityManager->persist($nutrient);
            $createdNutrientTypes++;
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            'Mock data loaded. Created: %d recipe types, %d nutrient types.',
            $createdRecipeTypes,
            $createdNutrientTypes
        ));

        return Command::SUCCESS;
    }
}
