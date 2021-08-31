<?php

namespace Karkov\Kcms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Entity\Site;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function findPageBySiteAndSlug(Site $site, PageSlug $pageSlug): ?Page
    {
        return $this->createQueryBuilder('p')
            ->where(':pageSlug MEMBER OF p.pageSlugs')
            ->setParameter('pageSlug', $pageSlug)
            ->andWhere(':site MEMBER OF p.sites')
            ->setParameter('site', $site)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
