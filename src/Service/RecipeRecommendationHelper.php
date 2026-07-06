<?php 


namespace App\Service;

use App\Entity\Recipe;
use App\Entity\RecipeView;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\RecipeViewRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipeRecommendationHelper
{
    public function __construct(
        private  RecipeRepository $reciperepository,
        private  RecipeViewRepository $recipeviewrepository,
        private  EntityManagerInterface $entityManager
    ){}
    public function logRecipeview(?User $user, Recipe $recipe): void
    {
        if(!$user) {
            return;
        }
        $hasViewed  = $this->recipeviewrepository->hasUserViewedRecipe($user, $recipe);

        if(!$hasViewed) {
            $recipeView = new RecipeView();
            $recipeView -> setRecipe($recipe);
            $recipeView -> setUser($user);
            $recipeView ->setViewedAt(new \DateTime());

            $this->entityManager->persist($recipeView);
            $this->entityManager->flush();
        }
    }

    public function  getRecommendations(Recipe $recipe) : array 
    {
        $currentHour = (int) (new \DateTime())->format('H');
        $mealType = 'Breakfast';

        if ($currentHour >= 11 && $currentHour < 16) {
            $mealType = 'Lunch';
        } elseif ($currentHour >= 16 && $currentHour < 19) {
            $mealType = 'Snack';
        } elseif ($currentHour >= 19 || $currentHour < 5) {
            $mealType = 'Dinner';
        }
         
          $relatedCuisine = $this->reciperepository->findSimilarCuisineByTime(
            $recipe->getRegion()->getId(),
            $recipe->getId(),
            $mealType
        );

        $chefSpecials = $this->reciperepository->findChefSpecialByTime(
            $recipe->getChef()->getId(),
            $recipe->getId(),
            $mealType
        );

        return [
            'mealType' => $mealType,
            'relatedCuisine' => $relatedCuisine,
            'chefSpecials' => $chefSpecials,
        ];
    }
}