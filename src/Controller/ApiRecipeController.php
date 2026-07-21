<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class ApiRecipeController extends AbstractController
{

    #[Route('/api/recipes', name: 'api_recipe_list', methods: ["GET"])]
    public function list(
        RecipeRepository $recipeRepository,
        SerializerInterface $serializer
    ): JsonResponse {

        $recipes = $recipeRepository->findAll();

        $json = $serializer->serialize($recipes, 'json', [
            'groups' => ['recipe:read']
        ]);


        return new JsonResponse($json, 200, [], true);
    }


    #[Route('/api/my-recipes', name: 'api_my_recipes', methods: ['GET', 'POST'])]
    public function myRecipes(RecipeRepository $recipeRepository): JsonResponse
    {
        $user = $this->getUser();

        $recipes = $recipeRepository->findBy(['chef' => $user]);
        if (!$user) {
            throw new NotFoundHttpException('User not found or not authenticated!');
        }
        $cleanData = [];
        foreach ($recipes as $recipe) {
            $cleanData[] = [
                'id' => $recipe->getId(),
                'title' => $recipe->getTitle(),
                'instructions' => $recipe->getInstructions(),
                'baseServings' => $recipe->getBaseServings(),
                'isVeg' => $recipe->isVeg(),
                'mealType' => $recipe->getMealType(),
            ];
        }
        return $this->json($cleanData, 200);
    }
    #[Route('api/recipes/create', name:'api_recipe_create',methods:["POST"])]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $user =$this->getUser();
        $user  = $entityManager->getRepository(User::class)->find($user);
        if(!$user){
            throw new NotFoundHttpException('User not found ');
        }
        $title = $request->request->get('title');
        $instructuions =$request->request->get('instructions');
        $baseServings =$request->request->get('baseServings',null,FILTER_VALIDATE_INT);
        $isVeg = $request->request->get('isVeg', null, FILTER_VALIDATE_BOOL);
        $mealType = $request->request->get('mealtype');
        $region = $request->request->get('region');


        $ingredients = $request->request->all('ingredients');


        $recipe = new Recipe();
        $recipe->setTitle($title);
        $recipe->setInstructions($instructuions);
        $recipe->setRegion($region);
        $recipe->setBaseServings($baseServings);
        $recipe->setIsVeg($isVeg);
        $recipe->setMealType($mealType);

        $recipe ->setChef($user);


        if (is_array($ingredients)) {
        foreach ($ingredients as $ingredientName) {
            if (!empty($ingredientName)) {
                $ingredient = new \App\Entity\Ingredient();
                $ingredient->setName($ingredientName); 
                $ingredient->setRecipe($recipe);
                $entityManager->persist($ingredient);
            }
        }
    }

        $imageFile =$request->files->get('image');
        if($imageFile){
            $newFilename = uniqid().'.'. $imageFile->guessExtension();
            $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/recipes/images',$newFilename);
            $recipe->setImage($newFilename);
        }
        $error = $validator->validate($recipe);
        if($error->count()>0){
            $errors = [];
            foreach($error as $error){
                $errors[] = $error->getMessage();
            }
            return $this->json(['error'=>$errors],400);
        }
        $entityManager->persist($recipe);
        $entityManager->flush();

        throw new NotFoundHttpException('Recipe createed successfully'
        );
    }

    // #[Route('/api/recipes/create', name:'api_recipe_create',methods:["POST"])]
    // public function create(
    //     EntityManagerInterface $entityManager,
    //     SerializerInterface $serializer,
    //     ValidatorInterface $validator,
    //     Request $request
    // ): JsonResponse
    // {

    //     $user = $this->getUser();
    //     if(!$user){
    //         return $this->json(['error' =>'you must be login fast and there come here!..'],401);
    //     }

    //     $recipe = $serializer->deserialize(
    //         $request->getContent(),
    //         Recipe::class,
    //         'json',
    //         ['groups' => ['recipe:write']]
    //     );

    //     $errors = $validator->validate($recipe);

    //     if(count($errors) > 0){
    //         $errorMessages = [];
    //         foreach ($errors    as $error){
    //             $errorMessages[] = $error->getMessage();
    //         }
    //         return $this->json(['error' => $errorMessages],400);
    //     }

    //     $data = json_decode($request->getContent(), true);
    //     if (!isset($data['chef_id'])) {
    //         return $this->json(['error' => 'Chef ID is required'], 400);
    //     }
    //         $user = $entityManager->getRepository(User::class)->find($data['chef_id']);
    //         if (!$user) {
    //             return $this->json(['error' => 'Chef not found'], 404);
    //     }

    //     $recipe->setChef($user);

    //     $errors = $validator->validate($recipe);
    //     if (count($errors) > 0) {
    //         $errorMessages = [];
    //         foreach ($errors as $error) {
    //             $errorMessages[$error->getPropertyPath()] = $error->getMessage();
    //         }
    //         return $this->json(['errors' => $errorMessages], 400);
    //     }

    //     $entityManager->persist($recipe);
    //     $entityManager->flush();

    //     return $this-> json(['message' => 'Recipe created successfully!'],201);
    // }


    



    #[Route('/api/recipe/{id}', name: 'api_recipe_update', methods: ['PUT','POST','GET'])]
    public function update(
        int $id,
        RecipeRepository $recipeRepository,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $recipe = $recipeRepository->find($id);
        if (!$recipe) {
            throw new NotFoundHttpException('Recipe Not Found!');
        }

        if ($recipe->getChef() !== $this->getUser()) {
            throw new NotFoundHttpException('You are not authorized to update this recipe!');
            }
        $serializer->deserialize(
            $request->getContent(),
            Recipe::class,
            'json',
            [
                'groups' => ['recipe:write'],
                'object_to_populate' => $recipe
            ]
        );

        if (isset($data['chef_id'])) {
            $user = $entityManager->getRepository(User::class)->find($data['chef_id']);
            if (!$user) {
                $recipe->setChef($user);
            }
        }

        $entityManager->flush();


        return $this->json([
            'message' => 'Recipe updated successfully!',
        ], 200);
    }

    #[Route('/api/recipe/{id}/img', name:'api_recipe_img_delete', methods:['DELETE'])]
    public function deleteImg(int $id,RecipeRepository $recipeRepository,EntityManagerInterface $entityManager):JsonResponse
    {
        $recipe = $recipeRepository->find($id);
        if(!$recipe){
            throw new NotFoundHttpException('Recipe is Not found!..');
        }

        if($recipe->getChef() !== $this->getUser()){
            throw new NotFoundHttpException('You are not authorized to delete this image!..');
        }

        $imagePath = $recipe->getImage();
        if($imagePath && file_exists($this->getParameter('kernel.project_dir') . '/public/' . $imagePath)){
            unlink($this->getParameter('kernel.project_dir') . '/public/' . $imagePath);
        }

        $recipe->setImage(null);
        $entityManager->flush();

        return $this->json([
            'message' => 'Recipe image deleted successfully!..'
        ],200);

    }
    #[Route('/api/recipe/{id}', name: 'api_recipe_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        RecipeRepository $recipeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $recipe = $recipeRepository->find($id);

        if (!$recipe) {
            throw new NotFoundHttpException('Recipe Not Found!');
        }

        if ($recipe->getChef() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException('YOu are not The  owener of this recipe!..');
        }

        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->json([
            'message' => 'recipe deleted succssessfully!.',
        ], 200);
    }


    #[Route('/api/recipe/{id}/upload-image', name: 'api_recipe_image_folder', methods: ['POST'])]
    public function uploadImage(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        RecipeRepository $recipeRepository
    ): JsonResponse {
        $recipe = $recipeRepository->find($id);

        if (!$recipe) {
            throw new NotFoundHttpException('Recipe is not founded!');
        }

        $uploadedFile = $request->files->get('image');

        if (!$uploadedFile) {
            throw new NotFoundHttpException('No image file provided!');
        }

        $uplodedFile = $request->files->get('image');

        if (!$uplodedFile) {
            throw new NotFoundHttpException('No image file provided!');
        }

        $newFilename = uniqid() . '.' . $uplodedFile->guessExtension();

        try {
            $uplodedFile->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/recipes',
                $newFilename
            );
        } catch (FileException $e) {
            throw new NotFoundHttpException('Failed to save image!..');
        }

        $recipe->setImage($newFilename);
        $entityManager->flush();
        return $this->json([
            'message' => 'Recipe image uploade successfully!.',
            'image_name' => $newFilename
        ], 200);
    }
}
