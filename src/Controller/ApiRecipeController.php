<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiRecipeController extends AbstractController
{
    
    #[Route('/api/recipes', name: 'api_recipe_list', methods: ["GET"])]
    public function list(RecipeRepository $recipeRepository,
    SerializerInterface $serializer
    ): JsonResponse 
    {
      
        $recipes = $recipeRepository->findAll();

        $json = $serializer->serialize($recipes, 'json',[
            'groups' => ['recipe:read']
        ]);
        

        return new JsonResponse($json,200,[],true);
    }


    #[Route('/api/my-recipes', name:'api_my_recipes', methods:['GET','POST'])]
    public function myRecipes(RecipeRepository $recipeRepository): JsonResponse
    {
        $user = $this->getUser();

       

        $recipes = $recipeRepository->findBy(['chef' => $user]);
       if(!$user){
         return new JsonResponse(['error' => 'User not found or not authenticated!'],401);
       }
       $cleanData=[];
       foreach ($recipes as $recipe) {
        $cleanData[] = [
            'id' =>$recipe->getId(),
            'title' =>$recipe->getTitle(),
            'instructions' =>$recipe->getInstructions(),
            'baseServings' =>$recipe->getBaseServings(),
            'isVeg' => $recipe->isVeg(),
            'mealType' => $recipe->getMealtype(),
         ];
        }
         return $this->json($cleanData,200);
    }

    #[Route('/api/recipes/create', name:'api_recipe_create',methods:["POST"])]
    public function create(EntityManagerInterface $entityManager,
    SerializerInterface $serializer,
    ValidatorInterface $validator,
    Request $request
    ): JsonResponse
    {

        $user = $this->getUser();
        if(!$user){
            return $this->json(['error' =>'you must be login fast and there come here!..'],401);
        }

        $recipe = $serializer->deserialize(
            $request->getContent(),
            Recipe::class,
            'json',
            ['groups' => ['recipe:write']]
        );

        $errors = $validator->validate($recipe);
        if(count($errors) > 0){
            $errorMessages = [];
            foreach ($errors    as $error){
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['error' => $errorMessages],400);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['chef_id'])) {
            return $this->json(['error' => 'Chef ID is required'], 400);
        }
            $user = $entityManager->getRepository(User::class)->find($data['chef_id']);
            if (!$user) {
                return $this->json(['error' => 'Chef not found'], 404);
        }

        $recipe->setChef($user);
        $entityManager->persist($recipe);
        $entityManager->flush();

        return $this-> json(['message' => 'Recipe created successfully!'],201);
    }






    #[Route('/api/recipe/{id}', name:'api_recipe_update', methods:['PUT'])]
    public function update(
        int $id,
        RecipeRepository $recipeRepository,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): JsonResponse 
    {
        $recipe = $recipeRepository->find($id);
        if(!$recipe){
            return $this->json([
                'error'=>'Recipe Not Found!'
            ],404);
        }

        if($recipe->getChef() !== $this->getUser()){
            return $this->json([
                'error'=>'You are not authorized to update this recipe!'
            ],403);
        }
        $serializer->deserialize(
            $request->getContent(),
            Recipe::class,
            'json',
            ['groups' => ['recipe:write'], 
            'object_to_populate' => $recipe
            ]
        );

        if(isset($data['chef_id'])){
            $user = $entityManager->getRepository(User::class)->find($data['chef_id']);
            if(!$user){
            $recipe->setChef($user);
            }
        }

        $entityManager->flush();

        
       return $this->json([
        'message' => 'Recipe updated successfully!',
         ],200);
    }

    #[Route('/api/recipe/{id}',name:'api_recipe_delete',methods:['DELETE'])]
    public function delete(
        int $id,
        RecipeRepository $recipeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse 
    {
        $recipe = $recipeRepository->find($id);

        if(!$recipe){
            return $this->json([
                'error'=>'Recipe Not Found!'
            ],404);
        }

        if($recipe->getChef() !== $this->getUser()){
            return new  JsonResponse(['error' =>'YOu are not The  owener of this recipe!..'],403);
        }

        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->json([
            'message' => 'recipe deleted succssessfully!.',
        ],200);
    }
}   