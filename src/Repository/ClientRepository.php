<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getNextNumClient()
    {
        try {
            $client = $this->createQueryBuilder('c')
                ->orderBy("c.numClient", "desc")
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
            if ($client != null) {
                return $client->getNumClient() + 1;
            } else {
                return 0;
            }
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getLastAdded()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'desc')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
