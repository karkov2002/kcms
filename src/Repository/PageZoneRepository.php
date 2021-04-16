<?php

namespace Karkov\Kcms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Karkov\Kcms\Entity\PageZone;

/**
 * @method PageZone|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageZone|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageZone[]    findAll()
 * @method PageZone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageZone::class);
    }
}
