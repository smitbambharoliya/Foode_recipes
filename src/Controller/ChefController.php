<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeView;
use App\Entity\Region;
use App\Form\RecipeType;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[IsGranted('ROLE_CHEF')]
final class ChefController extends AbstractController
{
  #[Route('/chef/dashboard', name:'app_chef_dashboard')]
  public function dashboard(RecipeRepository $recipeRepository, RegionRepository $regionRepository,EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_CHEF');
    $recipes = $recipeRepository->findBy(['chef'=>$this->getUser()]);
    $recipeCount = count($recipes);
    
    // Sort to get the most recent ones (assuming higher ID = more recent, or just array reverse)
    $recentRecipes = array_slice(array_reverse($recipes), 0, 3);
    $regions = $regionRepository->findAll();
    
    $entityManager->flush();
    return $this->render('chef/dashboard.html.twig',[
        'recipeCount' => $recipeCount,
        'recentRecipes' => $recentRecipes,
        'regions' => $regions,
    ]);
  }

  #[Route('/chef/recipe', name:'app_chef_recipe')]
  public function recipe(RecipeRepository $recipeRepository, Request $request): Response
  {
    $recipes = $recipeRepository->findBy(['chef'=>$this->getUser()]);
    return $this->render('chef/recipe.html.twig',[
        'recipes' => $recipes,
    ]);
  }

  #[Route('/chef/recipe/new',name:'app_chef_recipe_new')]
  public function new(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, RegionRepository $regionRepository,IngredientRepository $ingredient ):Response
  {
    $recipe = new Recipe();
    // Set the current user as the chef automatically
    $recipe->setChef($this->getUser());

    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);

    if($form->isSubmitted()) {
        if ($form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/recipes',
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an issue uploading your image.');
                }

                $recipe->setImage($newFilename);
            }
            $entityManager->persist($recipe);
            
            foreach ($recipe->getIngredients()as $singleIngredient) {
                $singleIngredient->setRecipe($recipe);
                $entityManager->persist($singleIngredient);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Recipe created successfully!');
            return $this->redirectToRoute('app_chef_recipe');
        } else {
            $this->addFlash('error', 'Please check the form for errors. Some fields are invalid.');
        }
    }
    $regions = $regionRepository->findAll();
    return $this->render('chef/recipe_new.html.twig',[
        'form' => $form->createView(),
        'regions' => $regions,
    ]);
  }

  

  #[Route('/recipe/show/{id}',name:'app_chef_recipe_show')]
  public function show(Recipe $recipe):Response
  {
    if (!$recipe) {
        throw $this->createNotFoundException('No recipe found.');
    }

    if ($recipe->getChef() !== $this->getUser()) {
        throw $this->createAccessDeniedException('You do not own this recipe.');
    }

    return $this->render('chef/recipe_show.html.twig',[
       'recipe'=>$recipe,
    ]);
  }

  #[Route('/recipe/edit/{id}',name:'app_chef_recipe_edit')]
  public function edit(Recipe $recipe, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, RegionRepository $regionRepository):Response
  {
    if (!$recipe) {
        throw $this->createNotFoundException('No recipe found.');
    }

    if ($recipe->getChef() !== $this->getUser()) {
        throw $this->createAccessDeniedException('You do not own this recipe.');
    }

    $form = $this->createForm(RecipeType::class, $recipe);
    
    $form->handleRequest($request);

    if($form->isSubmitted()) {
        if ($form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/recipes',
                        $newFilename
                    );
                    $recipe->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an issue uploading your image.');
                }
            }

            foreach ($recipe->getIngredients() as $singleIngredient) {
                $singleIngredient->setRecipe($recipe);
                $entityManager->persist($singleIngredient);
            }

            $entityManager->flush();
            $this->addFlash('success', 'recipe.flash.updated');
            return $this->redirectToRoute('app_chef_recipe');
        } else {
            $this->addFlash('error', 'Please check the form for errors. Some fields are invalid.');
        }
    }
    
    $regions = $regionRepository->findAll();
    return $this->render('chef/recipe_edit.html.twig',[
       'form'=>$form->createView(),
       'regions' => $regions,
       'recipe' => $recipe,
    ]);
  }

  #[Route('/recipe/delete/{id}',name:'app_chef_recipe_delete', methods:['POST','DELETE'])]
  public function delete(Recipe $recipe, EntityManagerInterface $entityManage):Response
  {
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


// navi rechipe nakhi hake  aek navapagge ma  