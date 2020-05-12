<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    
    public function findLast($count) {
        $qb = $this
            ->createQueryBuilder('post')
            ->orderBy('post.createdAt', 'DESC')
            ->setMaxResults($count);
        
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
    
    public function findAllPagine($page, $count) {
        $qb = $this->createQueryBuilder('post')
                ->add('orderBy', 'post.nbVotes DESC, post.createdAt DESC');
        
        $query = $qb->getQuery();
        
        $premierResultat = ($page - 1) * $count;
        $query->setFirstResult($premierResultat)->setMaxResults($count);
        $paginator = new Paginator($query);
        
        return $paginator;
    }

    public function findOneById($value): ?Post
    {
        return $this->createQueryBuilder('post')
            ->andWhere('post.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findAllTitle($value, $page, $count)
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.title LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('p.nbVotes', 'DESC')
            ->addorderBy('p.createdAt', 'DESC')
            ->getQuery()
        ;
        
        $premierResultat = ($page - 1) * $count;
        $query->setFirstResult($premierResultat)->setMaxResults($count);
        $paginator = new Paginator($query);
        
        return $paginator;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
