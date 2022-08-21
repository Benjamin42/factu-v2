<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

	public function getFollowedStatProduct() {
	    $query = $this->getEntityManager()
	        ->createQuery('SELECT p FROM FactuAppBundle:Product p WHERE p.isFollowedStat = 1');
	        
	    try {
	    	return $query->getResult(); 
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
