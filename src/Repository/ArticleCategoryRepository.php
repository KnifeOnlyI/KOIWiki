<?php

namespace App\Repository;

use App\Entity\ArticleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleCategory[]    findAll()
 * @method ArticleCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleCategory::class);
    }

    /**
     * Search in all categories
     *
     * @param string $query The search query
     *
     * @return ArticleCategory[] The article category list
     */
    public function search(string $query): array
    {
        $q = strtolower($query);

        return $this
            ->createQueryBuilder('articleCategory')
            ->where('lower(articleCategory.name) LIKE \'%' . $q . '%\' OR lower(articleCategory.name) LIKE \'%' . $q . '%\'')
            ->orderBy('articleCategory.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ArticleCategory[] Returns an array of ArticleCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleCategory
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
