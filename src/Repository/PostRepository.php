<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class PostRepository
 * @package App\Repository
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

    /**
     * Find the last post uploaded
     * @param $count int number of items epr page
     * @return mixed
     */
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

    /**
     * Find all posts and pagine them following count
     * @param $page int page we are looking for
     * @param $count int number of items epr page
     * @return Paginator
     */
    public function findAllPagine($page, $count) {
        $qb = $this->createQueryBuilder('post')
                ->add('orderBy', 'post.nbVotes DESC, post.createdAt DESC');
        
        $query = $qb->getQuery();
        
        $premierResultat = ($page - 1) * $count;
        $query->setFirstResult($premierResultat)->setMaxResults($count);
        $paginator = new Paginator($query);
        
        return $paginator;
    }

    /**
     * Find all the posts, sort them and pagin them
     * @desc Order posts by date or by vote, using pagination
     * @param $page int page we are looking for
     * @param $count int number of items epr page
     * @param $type string category after which we sort
     * @return Paginator
     */
    public function findAllPagineSorted($page, $count, $type) {
        $orderQuery = '';
        if($type == "newest-posts") {
            $orderQuery ='post.createdAt DESC';
        } else {
            $orderQuery = 'post.nbVotes DESC, post.createdAt DESC';
        }

        $qb = $this->createQueryBuilder('post')
            ->add('orderBy', $orderQuery);

        $query = $qb->getQuery();

        $firstResult = ($page - 1) * $count;
        $query->setFirstResult($firstResult)->setMaxResults($count);
        $paginator = new Paginator($query);

        return $paginator;
    }

    /**
     * Find a specific post
     * @param $value int post id
     * @return Post|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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
