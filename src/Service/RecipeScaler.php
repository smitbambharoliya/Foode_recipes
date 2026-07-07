<?php 

namespace App\Service;

use App\Entity\Recipe;

class RecipeScaler 
{
    public function scaleIngredients(Recipe $recipe, int $currentServings, int $baseServings = 2): array
    {
        $scaledIngredients = [];

        foreach ($recipe->getIngredients() as $ingredient) {
            $calculatedAmount = ($ingredient->getBaseQuantity() * $currentServings) / $baseServings;
            
            $calculatedAmount = round($calculatedAmount, 1);

            $scaledIngredients[] = [
                'name' => $ingredient->getName(),
                'amount' => $calculatedAmount,
                'unit' => $ingredient->getUnit(),
            ];   
        }

        return $scaledIngredients;
    }
}