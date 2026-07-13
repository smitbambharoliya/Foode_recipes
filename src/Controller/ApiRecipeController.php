<?php 

namespace  App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiRecipeController extends AbstractController
{
    public function list(RecipeRepository $recipeRepository): JsonResponse
    {
         $recipe = $recipeRepository->findAll();

         $data = [];
         foreach($recipe as $recipe){

            $data[] = [
                'id' => $recipe->getId(),
                'title' =>$recipe->getTitle(),
                'instructions' => $recipe->getInstructions(),
                'mealType' => $recipe->getMealType(),
                'isVeg' => $recipe->isVeg(),
            ];

         }
         return $this->json($data);
    }
}