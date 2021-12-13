<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Get the last specified number of public number
     *
     * @param int $number The number of articles to get
     *
     * @return Article[] The article list
     */
    public function getLastPublicArticles(int $number = 10): array
    {
        return $this
            ->createQueryBuilder('article')
            ->orderBy('article.id', 'DESC')
            ->where('article.isPublic = :val')
            ->setMaxResults($number)
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all accessible articles for an admin
     *
     * @return Article[] The article list
     */
    public function getAllForAdmin(): array
    {
        return $this
            ->createQueryBuilder('article')
            ->orderBy('article.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all accessible articles for the specified user
     *
     * @param User $user The connected user
     *
     * @return Article[] The article list
     */
    public function getAllForUser(UserInterface $user): array
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getAllForAdmin();
        } else {
            return $this
                ->createQueryBuilder('article')
                ->where('article.isPublic = true OR article.author = :authorVal')
                ->orderBy('article.id', 'ASC')
                ->setParameter('authorVal', $user)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Get all accessible articles for an anonymous user
     *
     * @return Article[] The article list
     */
    public function getAllForAnonymousUser(): array
    {
        return $this->findBy(['isPublic' => true], ['id' => 'ASC']);
    }

    /**
     * Search in all public and private articles
     *
     * @param string $query The search query
     *
     * @return Article[] The article list
     */
    public function searchInPublicOrPrivate(string $query): array
    {
        $q = strtolower($query);

        return $this
            ->createQueryBuilder('article')
            ->where('lower(article.title) LIKE \'%' . $q . '%\' OR lower(article.content) LIKE \'%' . $q . '%\'')
            ->orderBy('article.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search in all public and private user's authored article
     *
     * @param User $user The connected user
     * @param string $query The search query
     *
     * @return Article[] The article list
     */
    public function searchInPublicAndPrivateUserAuthored(UserInterface $user, string $query): array
    {
        $q = strtolower($query);

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->searchInPublicOrPrivate($q);
        } else {
            return $this
                ->createQueryBuilder('article')
                ->where('(lower(article.title) LIKE \'%' . $q . '%\' OR lower(article.content) LIKE \'%' . $q . '%\') AND 
                (article.isPublic = true OR article.author = :authorVal)')
                ->orderBy('article.id', 'ASC')
                ->setParameter('authorVal', $user)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Search all accessible articles for an anonymous user
     *
     * @param string $query The search query
     *
     * @return Article[] The article list
     */
    public function searchInPublic(string $query): array
    {
        $q = strtolower($query);

        return $this
            ->createQueryBuilder('article')
            ->where('(lower(article.title) LIKE \'%' . $q . '%\' OR lower(article.content) LIKE \'%' . $q . '%\') AND article.isPublic = true')
            ->orderBy('article.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
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
    public function findOneBySomeField($value): ?Article
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
