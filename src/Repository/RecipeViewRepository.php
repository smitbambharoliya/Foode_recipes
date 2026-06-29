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

    //    /**
    //     * @return RecipeView[] Returns an array of RecipeView objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RecipeView
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
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
}
