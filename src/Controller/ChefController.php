<?php

namespace App\Controller;

use App\DTO\RecipeInputDTO;
use App\Entity\Recipe;
use App\Entity\Region;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\RegionRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_CHEF')]
final class ChefController extends AbstractController
{
  #[Route('/chef/dashboard', name:'app_chef_dashboard')]
  public function dashboard(RecipeRepository $recipeRepository, RegionRepository $regionRepository,EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_CHEF');
    $recipes = $recipeRepository->findBy(['chef'=>$this->getUser()]);
    $recipeCount = count($recipes);
    
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

 #[Route('/chef/recipe/new', name: 'app_chef_recipe_new')]
public function new(
    EntityManagerInterface $entityManager,
    Request $request, 
    RegionRepository $regionRepository,
    FileUploader $fileUploader
): Response {
    $dto = new RecipeInputDTO();
    $form = $this->createForm(RecipeType::class, $dto);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
           
        if ($form->isValid()) {
            
            $recipe = new Recipe();
            $recipe->setTitle($dto->title);
            $recipe->setInstructions($dto->instructions);
            $recipe->setBaseServings($dto->baseServings);
            $recipe->setMealType($dto->mealtype); 
            $recipe->setChef($this->getUser());
            $recipe->setIsVeg($dto->isVeg);

            $regionName = trim($dto->regionName ?? ''); 
            if ($regionName) {
                $region = $regionRepository->findOneBy(['name' => $regionName]);
                if (!$region) {
                    $region = new Region();
                    $region->setName($regionName);
                    $entityManager->persist($region);
                }
                $recipe->setRegion($region);
            }

       
            if ($dto->image) {
                $fileName = $fileUploader->upload($dto->image);
                if ($fileName) {
                    $recipe->setImage($fileName);
                } else {
                    $this->addFlash('error', 'There was an issue uploading your image.');
                }
            }      
            
            $entityManager->persist($recipe);

      
            $ingredientsList = $dto->ingredients ?? [];
            foreach ($ingredientsList as $singleIngredient) {
                $singleIngredient->setRecipe($recipe);
                $entityManager->persist($singleIngredient);
            }
            
            $entityManager->flush();
            
            
            $this->addFlash('success', 'Recipe created successfully!');
            return $this->redirectToRoute('app_chef_recipe');
        } else {
            $this->addFlash('error', 'Please check the form for errors. Some fields are invalid or missing.');
        }
    }

    $regions = $regionRepository->findAll();
    return $this->render('chef/recipe_new.html.twig', [
        'form' => $form->createView(),
        'region' => $regions,
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
  public function edit(
    Recipe $recipe,
    EntityManagerInterface $entityManager, 
    Request $request, 
    FileUploader $fileUploader,
    RegionRepository $regionRepository,
    ):Response
  {
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
    $dto->mealtype = $recipe->getMealtype();
    $dto->isVeg = $recipe->isVeg();
    $dto->regionName = $recipe->getRegion() ? $recipe->getRegion()->getName() : null;

    $form = $this->createForm(RecipeType::class,$dto);
    $form->handleRequest($request);

    if($form->isSubmitted()) {
        if ($form->isValid()) {
           
            $recipe->setTitle($dto->title);
            $recipe->setInstructions($dto->instructions);
            $recipe->setBaseServings($dto->baseServings);
            $recipe->setMealType($dto->mealtype);
            $recipe->setIsVeg($dto->isVeg);

            $regionName = trim($dto->regionName);
            if ($regionName) {
                $region = $regionRepository->findOneBy(['name' => $regionName]);
                if(!$region) {
                    $region = new Region();
                    $region->setName($regionName);
                    $entityManager->persist($region);
                }
                 $recipe->setRegion($region);
            } else{
                $recipe->setRegion(null);
            }
           
            if($dto->image){
                $newfilenmae = $fileUploader->upload($dto->image);
                if($newfilenmae){
                    $recipe->setImage($newfilenmae);
                }else{
                    $this->addFlash('error', 'There Are Someting  issue With the this file out imge!.');    
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