<?php

namespace Karkov\Kcms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Karkov\Kcms\Entity\PageSlug;

/**
 * @method PageSlug|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageSlug|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageSlug[]    findAll()
 * @method PageSlug[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageSlugRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageSlug::class);
    }

    public function findSlug($site, $local, $slug): ?PageSlug
    {
        return $this->createQueryBuilder('page_slug')
            ->innerJoin('page_slug.page', 'page')
            ->where(':site MEMBER OF page.sites')->setParameter('site', $site)
            ->andWhere('page_slug.slug = :slug')->setParameter('slug', $slug)
            ->andWhere('page_slug.local = :local')->setParameter('local', $local)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllSlugsBySiteAndLocal($site, $local): array
    {
        return $this->createQueryBuilder('page_slug')
            ->innerJoin('page_slug.page', 'page')
            ->where(':site MEMBER OF page.sites')->setParameter('site', $site)
            ->andWhere('page_slug.local = :local')->setParameter('local', $local)
            ->getQuery()
            ->getResult();
    }
}
