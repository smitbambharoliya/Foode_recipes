<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeView;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecipeView>
 */
class RecipeViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeView::class);
    }
    public function hasUserViewedRecipe(User $user,Recipe $recipe): bool
    {
        $oneHourAgo = new \DateTimeImmutable('-1 hour');

        $count = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->andWhere('v.user = :user')
            ->andWhere('v.recipe = :recipe')
            ->andWhere('v.viewedAt > :oneHourAgo')
            ->setParameter('user', $user)
            ->setParameter('recipe', $recipe)
            ->setParameter('oneHourAgo', $oneHourAgo)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
   
    public function findUserHistory(User $user): array
    {
    return $this->createQueryBuilder('v')
        ->select('v', 'MAX(v.viewedAt) as lastViewed')
 
        ->join('v.recipe', 'r')
        
      
            ->andWhere('v.user = :user')
            ->setParameter('user', $user)
        
        
            ->groupBy('r.id')
        

            ->orderBy('lastViewed', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }
}
