<?php

namespace App\Controller;

use App\DTO\RecipeInputDTO;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\RegionRepository;
use App\Service\FileUploader;
use App\Service\RecipeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_CHEF')]
final class ChefController extends AbstractController
{
    #[Route('/chef/dashboard', name: 'app_chef_dashboard')]
    public function dashboard(
        RecipeRepository $recipeRepository,
        RegionRepository $regionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $recipes = $recipeRepository->findBy(['chef' => $this->getUser()]);
        $recipeCount = count($recipes);

        $recentRecipes = array_slice(array_reverse($recipes), 0, 3);
        $regions = $regionRepository->findAll();

        $entityManager->flush();
        return $this->render('chef/dashboard.html.twig', [
            'recipeCount' => $recipeCount,
            'recentRecipes' => $recentRecipes,
            'regions' => $regions,
        ]);
    }

    #[Route('/chef/recipe', name: 'app_chef_recipe')]
    public function recipe(
        RecipeRepository $recipeRepository,
        Request $request
    ): Response {
        $recipes = $recipeRepository->findBy(['chef' => $this->getUser()]);
        return $this->render('chef/recipe.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/chef/recipe/new', name: 'app_chef_recipe_new')]
    public function new(
        Request $request,
        RegionRepository $regionRepository,
        RecipeService $recipeService,
    ): Response {

        $dto = new RecipeInputDTO();
        $form = $this->createForm(RecipeType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipeService->saveRecipe($dto, null, $this->getUser());

            $this->addFlash('success', 'Recipe is created successfuliy!');
            return $this->redirectToRoute('app_chef_recipe');
        }
        return $this->render('chef/recipe_new.html.twig', [
            'form' => $form->createView(),
            'region' => $regionRepository->findAll(),
        ]);
    }

    #[Route('/recipe/show/{id}', name: 'app_chef_recipe_show')]
    public function show(
        Recipe $recipe
    ): Response {
        if (!$recipe) {
            throw $this->createNotFoundException('No recipe found.');
        }

        if ($recipe->getChef() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not own this recipe.');
        }

        return $this->render('chef/recipe_show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipe/edit/{id}', name: 'app_chef_recipe_edit')]
    public function edit(
        Recipe $recipe,
        Request $request,
        RegionRepository $regionRepository,
        RecipeService $recipeService
    ): Response {
        if (!$recipe) {
            throw $this->createNotFoundException('No recipe found.');
        }

        if ($recipe->getChef() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not own this recipe.');
        }

        $dto = new RecipeInputDTO();
        $dto->title = $recipe->getTitle();
        $dto->instructions = $recipe->getInstructions();
        $dto->baseServings = $recipe->getBaseServings();
        $dto->ingredients = $recipe->getIngredients()->toArray();
        $dto->mealtype = $recipe->getMealType();
        $dto->isVeg = $recipe->isVeg();
        $dto->regionName = $recipe->getRegion() ? $recipe->getRegion()->getName() : null;

        $form = $this->createForm(RecipeType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeService->saveRecipe($dto, $recipe);

            $this->addFlash('success', 'Recipe Updated success!.');
            return $this->redirectToRoute('app_chef_recipe');
        }

        return $this->render('chef/recipe_edit.html.twig', [
            'form' => $form->createView(),
            'regions' => $regionRepository->findAll(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipe/delete/{id}', name: 'app_chef_recipe_delete', methods: ['POST', 'DELETE'])]
    public function delete(
        Recipe $recipe,
        EntityManagerInterface $entityManage
    ): Response {
        if (!$recipe) {
            throw $this->createNotFoundException('No recipe found.');
        }

        if ($recipe->getChef() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not own this recipe.');
        }

        $entityManage->remove($recipe);
        $entityManage->flush();
        $this->addFlash('success', 'recipe.flash.deleted');

        return $this->redirectToRoute('app_chef_recipe');
    }
}