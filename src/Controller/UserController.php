<?php

namespace App\Controller;

use App\Entity\RecipeView;
use App\Entity\Review;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Repository\RecipeViewRepository;
use App\Service\RecipeScaler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



final class UserController extends AbstractController
{
    #[Route('/home',name:"app_home")]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recipesWithViews = $recipeRepository->findAllWithViewCount();

        return $this->render('user/home.html.twig', [
            'recipes'=>$recipesWithViews,
        ]);
    }

    #[Route('/home/{id}',name:'app_home_show')]
    public function show(Request $request,
    RecipeRepository $recipeRepository,
    RecipeViewRepository $recipeViewRepository,
    EntityManagerInterface $entityManager,
    RecipeScaler $recipeScaler,
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
                $this->addFlash('error','You must be login to giv comment!');
                return $this->redirectToRoute('app_login');
            }

            $review->setRecipe($recipe);
            $review->setUser($user);
            $review->setCreatedAt(new \DateTime());
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success','Your comment has been added successfully!');
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

        $scaledIngredients = $recipeScaler->scaleIngredients($recipe, $servings, $recipe->getBaseServings());

        return $this->json([
            'servings' => $servings,
            'ingredients' => $scaledIngredients,
        ]);
    }
}
