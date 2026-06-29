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
  #[Route('/chef/deshbord', name:'app_chef_deshbord')]
  public function deshbord(RecipeRepository $recipeRepository, RegionRepository $regionRepository,EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_CHEF');
    $recipes = $recipeRepository->findBy(['chef'=>$this->getUser()]);
    $recipeCount = count($recipes);
    
    // Sort to get the most recent ones (assuming higher ID = more recent, or just array reverse)
    $recentRecipes = array_slice(array_reverse($recipes), 0, 3);
    $regions = $regionRepository->findAll();
    
    $entityManager->flush();
    return $this->render('chef/deshbord.html.twig',[
        'recipeCount' => $recipeCount,
        'recentRecipes' => $recentRecipes,
        'regions' => $regions,
    ]);
  }

  #[Route('/chef/recipie', name:'app_chef_recipie')]
  public function recipie(RecipeRepository $recipeRepository, Request $request): Response
  {
    $recipes = $recipeRepository->findBy(['chef'=>$this->getUser()]);
    return $this->render('chef/recipie.html.twig',[
        'recipes' => $recipes,
    ]);
  }

  #[Route('/chef/recipie/new',name:'app_chef_recipie_new')]
  public function new(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, RegionRepository $regionRepository,IngredientRepository $ingredient ):Response
  {
    $recipe = new Recipe();
    // Set the current user as the chef automatically
    $recipe->setChef($this->getUser());

    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $regionName = $form->get('region_name')->getData();
      if ($regionName) {
          $regionName = trim($regionName);
          $region = $regionRepository->findOneBy(['name' => $regionName]);
          if (!$region) {
              // Create new region if it doesn't exist
              $region = new Region();
              $region->setName($regionName);
              $entityManager->persist($region);
          }
          $recipe->setRegion($region);
      }
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
              // ... handle exception if something happens during file upload
          }

          $recipe->setImage($newFilename);
      }
      $entityManager->persist($recipe);
      
      foreach ($recipe->getIngredients()as $singleIngredient) {
        $singleIngredient->setRecipe($recipe);
        $entityManager->persist($singleIngredient);
      }
      
      $entityManager->flush();

     
     
      return $this->redirectToRoute('app_chef_recipie');
    }
    $regions = $regionRepository->findAll();
    return $this->render('chef/recipie_new.html.twig',[
        'form' => $form->createView(),
        'regions' => $regions,
    ]);
  }

  

  #[Route('/recipie/show/{id}',name:'app_chef_recipie_show')]
  public function show(Recipe $recipe):Response
  {
    if (!$recipe) {
        throw $this->createNotFoundException('No recipe found.');
    }


    return $this->render('chef/recipie_show.html.twig',[
       'recipe'=>$recipe,
    ]);
  }

  #[Route('/recipie/edit/{id}',name:'app_chef_recipie_edit')]
  public function edit(Recipe $recipe, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, RegionRepository $regionRepository):Response
  {
    if (!$recipe) {
        throw $this->createNotFoundException('No recipe found.');
    }

    $regionName = $recipe->getRegion() ? $recipe->getRegion()->getName() : '';
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->get('region_name')->setData($regionName);
    
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $newRegionName = $form->get('region_name')->getData();
      if ($newRegionName) {
          $newRegionName = trim($newRegionName);
          $region = $regionRepository->findOneBy(['name' => $newRegionName]);
          if (!$region) {
              $region = new Region();
              $region->setName($newRegionName);
              $entityManager->persist($region);
          }
          $recipe->setRegion($region);
      }
      
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
              // ... handle exception
          }
      }

      foreach ($recipe->getIngredients() as $singleIngredient) {
        $singleIngredient->setRecipe($recipe);
        $entityManager->persist($singleIngredient);
      }

      $entityManager->flush();
      $this->addFlash('success', 'Recipe has been updated successfully!');
      return $this->redirectToRoute('app_chef_recipie');
    }
    
    $regions = $regionRepository->findAll();
    return $this->render('chef/recipie_edit.html.twig',[
       'form'=>$form->createView(),
       'regions' => $regions,
       'recipe' => $recipe,
    ]);
  }

  #[Route('/recipie/delete/{id}',name:'app_chef_recipie_delete', methods:['POST','DELETE'])]
  public function delete(Recipe $recipe, EntityManagerInterface $entityManage):Response
  {
    if (!$recipe) {
        throw $this->createNotFoundException('No recipe found.');
    }

    $entityManage->remove($recipe);
    $entityManage->flush();
    $this->addFlash('success', 'Recipie has been deleted');
    
    return $this->redirectToRoute('app_chef_recipie');
  }

}


// navi rechipe nakhi hake  aek navapagge ma  