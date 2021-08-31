<?php

namespace Karkov\Kcms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Karkov\Kcms\Entity\ContentLocal;

/**
 * @method ContentLocal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentLocal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentLocal[]    findAll()
 * @method ContentLocal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentLocalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentLocal::class);
    }
}
