<?php

namespace App\Model;

class RecipeDTO
{
    public string $title;
    public int $diners;

    public RecipeTypeDTO $type;

    /** @var IngredientDTO[] */
    public array $ingredients = [];

    /** @var StepDTO[] */
    public array $steps = [];

    /** @var RecipeNutritionalValueDTO[] */
    public array $nutritionalValues = [];

    /** @var RatingDTO[] */
    public array $ratings = [];
}
