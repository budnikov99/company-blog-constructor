<?php

namespace App\Repository;

use App\Entity\Post;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $desc_orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $desc_orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    
    private function configureQuery(QueryBuilder $qb, int $start_from = 0, int $limit = 0, $catlist = null, DateTimeInterface $begin = null, DateTimeInterface $end = null, array $keywords = null, string $desc_order = 'ASC'){
        if(!empty($keywords)){
            foreach($keywords as $i => $word){
                $qb->orWhere('p.title LIKE :keyword'.$i);
                $qb->setParameter(':keyword'.$i, '%'.$word.'%');
            }
        }
        
        if(!empty($catlist)){
            if(!is_array($catlist)){
                $catlist = [$catlist];
            }
            $qb->andWhere('p.category IN(:catlist)')
            ->setParameter('catlist', $catlist);
        }

        if(!empty($begin) && !empty($end)){
            $qb->andWhere('p.creation_date BETWEEN :start AND :end')
            ->setParameter('start', $begin)
            ->setParameter('end', $end);
        }

        if($start_from > 0){
            $qb->setFirstResult($start_from);
        }

        if($limit > 0){
            $qb->setMaxResults($limit);
        }

        $qb->orderBy('p.id', $desc_order?'DESC':'ASC');
    }

    public function countByCategoriesAndDate($catlist = null, DateTimeInterface $begin = null, DateTimeInterface $end = null, array $keywords = null){
        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p.id)');

        $this->configureQuery($qb, 0, 0, $catlist, $begin, $end, $keywords, false);
        
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByCategoriesAndDate(int $start_from = 0, int $limit = 0, $catlist = null, DateTimeInterface $begin = null, DateTimeInterface $end = null, array $keywords = null, bool $desc_order = false){
        $qb = $this->createQueryBuilder('p');

        $this->configureQuery($qb, $start_from, $limit, $catlist, $begin, $end, $keywords, $desc_order);
        
        return $qb->getQuery()->getResult();
    }
}
