<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

   

    public function searchGlobal(?string $searchTerm, ?int $regionId = null): array
{
    $qb = $this->createQueryBuilder('r')
        ->select('r as recipe', 'COUNT(DISTINCT v.id) as viewCount') 
        ->leftJoin('r.recipeViews', 'v')
        ->leftJoin('r.region', 'reg')
        ->leftJoin('r.ingredients', 'ing')
        ->groupBy('r.id');

    if ($searchTerm) {
        $qb->andWhere(
            $qb->expr()->orX(
                'r.title LIKE :search',       
                'reg.name LIKE :search',     
                'ing.name LIKE :search'      
            )
        )
        ->setParameter('search', '%' . $searchTerm . '%');
    }

    if ($regionId) {
        $qb->andWhere('reg.id = :regionId')
           ->setParameter('regionId', $regionId);
    }

    return $qb->getQuery()->getResult();
}
 
    public function findAllWithViewCount(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r as recipe', 'COUNT(v.id) as viewCount')
            ->leftJoin('r.recipeViews', 'v')
            ->groupBy('r.id')
            ->getQuery()
            ->getResult();
    }

    public function findTrendingByTime(string $curentMealType):array
    {
       $totdayStart = new \DateTime('today 00:00:00');
       $todayEnd = new \DateTime('today 23:59:59');

       $qb = $this->createQueryBuilder('r')

                  ->addSelect( 'COUNT(v.id) as viewCount')

                  ->leftJoin('r.recipeViews', 'v')

                  ->where('r.meal_type = :mealType')

                  ->andWhere('v.viewedAt >= :todayStart AND v.viewedAt <= :todayEnd')

                  ->setParameter('mealType',$curentMealType)
                  ->setParameter('todayStart',$totdayStart)
                  ->setParameter('todayEnd',$todayEnd)

                  ->groupBy('r.id')

                  ->orderBy('viewCount','DESC')

                  ->setMaxResults(5);
                 
                  $results = $qb->getQuery()->getResult();

                  if (empty($results)) {

                    return $this->createQueryBuilder('r')

                  ->select('r as recipe', '0 as viewCount')

                  ->andWhere('r.meal_type = :mealType')

                  ->setParameter('mealType', $curentMealType)

                  ->setMaxResults(5)

                  ->getQuery()
                  ->getResult();
    }
    return $results;
            
    }

    public function findRecommendationsByTime(string $curentMealType):array
    {
        return $this->createQueryBuilder('r')
                    ->andWhere('r.meal_type = :mealType')
                    ->setParameter('mealType',$curentMealType)
                    ->setMaxResults(5)
                    ->getQuery()
                    ->getResult();
    }
    public function findNewlyAdded(): array
    {
        return $this->createQueryBuilder('r')
                    ->orderBy('r.createdAt','DESC')
                    ->setMaxResults(5)
                    ->getQuery()
                    ->getResult();
    }

    public function findSimilarCuisineByTime(int $regionId, int $currentRecipeId,string $curentMealType,int $limit = 4): array
    {
      return $this->createQueryBuilder('r')
                  ->andWhere('r.region = :regionId')
                  ->andWhere('r.id != :currentRecipeId')
                  ->andWhere('r.meal_type = :mealType')
                  ->setParameter('regionId', $regionId)
                  ->setParameter('currentRecipeId', $currentRecipeId)
                  ->setParameter('mealType',$curentMealType)
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }
    public function findChefSpecialByTime(int $chefId,int $currentRecipeId,string $mealType,int $limit =3): array
    {

        return $this->createQueryBuilder('r')
                    ->andWhere('r.chef = :chefId')
                    ->andWhere('r.id != :currentRecipeId')
                    ->andWhere('r.meal_type = :mealType')
                    ->setParameter('currentRecipeId', $currentRecipeId)
                    ->setParameter('mealType',$mealType)
                    ->setParameter('chefId',$chefId)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }
}


