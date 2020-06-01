<?php

namespace App\Repository;

use App\Entity\Post;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    
    public function findByCategoriesAndDate(int $start_from = -1, int $limit = 0, array $catlist = null, DateTimeInterface $start = null, DateTimeInterface $end = null){
        $qb = $this->createQueryBuilder('p');

        $flag = false;
        if(!is_null($catlist)){
            $qb->andWhere('p.category IN(:catlist)')
            ->setParameter('catlist', $catlist);
            $flag = true;
        }

        if(!is_null($start) && !is_null($end)){
            $qb->andWhere('p.creation_date BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
            $flag = true;
        }

        if($start_from >= 0){
            $qb->andWhere('p.id >= :startFrom')
            ->setParameter('startFrom', $start_from);
        }

        if($limit > 0){
            $qb->setMaxResults($limit);
        }

        if($flag){
            return $qb->getQuery()->getResult();
        }else{
            return null;
        }
    }
}
