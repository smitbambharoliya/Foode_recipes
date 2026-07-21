<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_landing')]
    public function index(
        RecipeRepository $recipeRepository
    ): Response
    {
        $latestRecipes = $recipeRepository->findNewlyAdded();
        return $this->render('home/index.html.twig',[
            'latest_recipes' => $latestRecipes,
            'trending_recipes' => $recipeRepository->findTrendingByTime('Breakfast'),
            'lunch_recipes' => $recipeRepository->findTrendingByTime('Lunch'),
            'dinner_recipes' => $recipeRepository->findTrendingByTime('Dinner'),
            
        ]);
    }
}
