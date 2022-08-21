<?php

namespace App\Repository;

use App\Entity\Price;
use App\Entity\ProductCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ProductCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCategory::class);
    }

	public function getFollowedStatCategories() {
	    $query = $this->getEntityManager()
	        ->createQuery('SELECT c FROM FactuAppBundle:ProductCategory c WHERE c.isFollowedStat = 1');
	        
	    try {
	    	return $query->getResult(); 
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}

    public function remove(ProductCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
