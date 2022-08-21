<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CountryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

	public function getDefaultCountry() {
	    $query = $this->getEntityManager()
	        ->createQuery('
	            SELECT c FROM FactuAppBundle:Country c
	            WHERE c.code = (SELECT p.pValue FROM FactuAppBundle:Parameter p WHERE p.pName = "code_pays_defaut")'
	        );
	        
	    try {
	        return $query->getSingleResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}
}
