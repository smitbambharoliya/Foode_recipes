<?php

namespace App\Controller;

use App\Entity\RecipeView;
use App\Entity\Review;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Repository\RecipeViewRepository;
use App\Repository\RegionRepository;
use App\Service\RecipeDateTimeHelper;
use App\Service\RecipeScaler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



final class UserController extends AbstractController
{
    #[Route('/home',name:"app_home")]
    public function index(RecipeRepository $recipeRepository, 
    RegionRepository $regionRepository, 
    RecipeDateTimeHelper $recipeDateTimeHelper,  
    Request $request): Response
    {
        $currentMealType = $recipeDateTimeHelper->getCurrentMealType();
        $displayTitle = $recipeDateTimeHelper->getDisplayTitle($currentMealType);

        $trendingRecipes = $recipeRepository->findTrendingByTime($currentMealType);
        $recommendedRecipes = $recipeRepository->findRecommendationsByTime($currentMealType);
        $newlyAdded = $recipeRepository->findNewlyAdded();

        $searchTerm = $request->query->get('search');
        $regionId = $request->query->get('region') ? (int) $request->query->get('region') : null;

        $recipesWithViews = $recipeRepository->searchGlobal($searchTerm, $regionId);
        $regions = $regionRepository->findAll();


        return $this->render('user/home.html.twig', [
            'recipes'=>$recipesWithViews,
            'regions'=>$regions,
            'selectedRegionId'=>$regionId,
            'searchTerm'=>$searchTerm,
            'displayTitle' => $displayTitle,
            'trendingRecipes' => $trendingRecipes,
            'recommendedRecipes' => $recommendedRecipes,
            'newlyAdded' => $newlyAdded,
        ]);
    }

    #[Route('/home/{id}',name:'app_home_show')]
    public function show(Request $request,
    RecipeRepository $recipeRepository,
    RecipeViewRepository $recipeViewRepository,
    EntityManagerInterface $entityManager,
    int $id): Response
    {
        $recipe=$recipeRepository->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('No recipe found for id ' . $id);
        }
        $user  = $this->getUser();

        if($user){
            $hasViewed = $recipeViewRepository->hasUserViewedRecipe($user,$recipe);

            if(!$hasViewed){
                $recipeView = new RecipeView();
                $recipeView->setRecipe($recipe);
                $recipeView->setUser($user);
                $recipeView->setViewedAt(new \DateTime());

                $entityManager->persist($recipeView);
                $entityManager->flush();
            }   
        }
        $review = new Review();
        $form = $this->createForm(CommentType::class,$review);
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid()){
            if(!$user) {
                $this->addFlash('error','recipe.flash.login_required');
                return $this->redirectToRoute('app_login');
            }

            $review->setRecipe($recipe);
            $review->setUser($user);
            $review->setCreatedAt(new \DateTime());
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success','recipe.flash.comment_added');
            return $this->redirectToRoute('app_home_show',['id'=>$id]);
        }
        return $this->render('user/home_show.html.twig',[
            'recipe'=>$recipe,
            'reviewForm' => $form->createView(),
        ]);
    }

    #[Route('/api/recipe/{id}/scale', name: 'api_recipe_scale', methods: ['GET'])]
    public function scale(int $id, Request $request, RecipeRepository $recipeRepository, RecipeScaler $recipeScaler): Response
    {
        $recipe = $recipeRepository->find($id);
        if (!$recipe) {
            return $this->json(['error' => 'Recipe not found'], Response::HTTP_NOT_FOUND);
        }

        $servings = $request->query->getInt('servings', $recipe->getBaseServings());
        if ($servings < 1) {
            return $this->json(['error' => 'Invalid servings'], Response::HTTP_BAD_REQUEST);
        }

        $baseServings = $recipe->getBaseServings();
        if ($baseServings < 1) {
            return $this->json(['error' => 'Recipe has invalid base servings configuration'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $scaledIngredients = $recipeScaler->scaleIngredients($recipe, $servings, $baseServings);

        return $this->json([
            'servings' => $servings,
            'ingredients' => $scaledIngredients,
        ]);
    }
}
