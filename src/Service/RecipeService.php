<?php 

namespace App\Service;

use App\DTO\RecipeInputDTO;
use App\Entity\Recipe;
use App\Entity\Region;
use App\Entity\User;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipeService 
{
    public function __construct(
    private RegionRepository $regionrepository,
    private EntityManagerInterface $entityManager,
    private FileUploader $fileUploader,

    )
    {
    }
    public function saveRecipe(RecipeInputDTO $dto, ?Recipe $recipe = null, ?User $chef = null): Recipe
    {
        $recipe = $recipe ?? new  Recipe();
        
        $recipe ->setTitle($dto->title);
        $recipe->setInstructions($dto->instructions);
        $recipe->setBaseServings($dto->baseServings);
        $recipe->setMealType($dto->mealtype);
        $recipe->setIsVeg($dto->isVeg);
                 

        if ($chef && !$recipe->getChef()) {
            $recipe->setChef($chef);
        }


        $regionName = trim($dto->getregionName ?? '');
        if ($regionName){
            $region = $this->regionrepository->findOneBy(['name'=>$regionName]);
            if (!$region){
                $region = new Region();
                $region->setName($regionName);
                $this->entityManager->persist($region);
            }
            $recipe->setRegion($region);
        } else {
            $recipe ->setRegion(null);
        }

        if($dto->image){
            $fileName = $this->fileUploader->Upload($dto->image);
            if($fileName){
                $recipe->setImage($fileName);
            }
        }
       $this->entityManager->persist($recipe);
       
       $ingredientsList = $dto->ingredients ?? [];
        foreach ($ingredientsList as $singleIngredient) {
            $singleIngredient->setRecipe($recipe);
            $this->entityManager->persist($singleIngredient);
        }

        $this->entityManager->flush();

        return $recipe;
    }
    
} 