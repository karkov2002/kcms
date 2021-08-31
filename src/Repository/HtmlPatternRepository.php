<?php

namespace Karkov\Kcms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Karkov\Kcms\Entity\HtmlPattern;

/**
 * @method HtmlPattern|null find($id, $lockMode = null, $lockVersion = null)
 * @method HtmlPattern|null findOneBy(array $criteria, array $orderBy = null)
 * @method HtmlPattern[]    findAll()
 * @method HtmlPattern[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HtmlPatternRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HtmlPattern::class);
    }
}
