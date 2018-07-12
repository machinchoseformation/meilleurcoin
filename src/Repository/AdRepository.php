<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AdRepository extends ServiceEntityRepository
{
    public function findHomeAds($categoryId = null, $keyword = null)
    {
        // DQL
        /*
        $dql = "SELECT a 
                FROM App\Entity\Ad a
                WHERE a.category = :cat 
                ORDER BY a.dateCreated DESC";

        $query = $this->getEntityManager()->createQuery($dql);
        */

        // QueryBuilder
        $qb = $this->createQueryBuilder("a");
        //si on a un id de catégorie, on ajoute le where
        if ($categoryId) {
            $qb->andWhere("a.category = :cat");
            $qb->setParameter("cat", $categoryId);
        }

        if ($keyword){
            $qb->andWhere("(a.title LIKE :kw 
                            OR 
                            a.description LIKE :kw)");
            $qb->setParameter("kw", "%$keyword%");
        }

        $qb->addOrderBy("a.dateCreated", "DESC");

        //pour éviter les 10000 requêtes, on fait la jointure
        $qb->join('a.creator', 'c');
        $qb->addSelect('c');
        $qb->join('a.pictures', 'p');
        $qb->addSelect('p');

        $query = $qb->getQuery();

        //commun au DQL et QueryBuilder
        //$query->setParameter("cat", $categoryId);
        $query->setMaxResults(50);
        $ads = $query->getResult();

        return $ads;
    }


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ad::class);
    }

}
