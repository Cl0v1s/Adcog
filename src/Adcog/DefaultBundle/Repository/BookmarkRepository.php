<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Bookmark;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class BookmarkRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class BookmarkRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return Bookmark[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyLikeFilter($qb, 'title', $filters)
            ->applyLikeFilter($qb, 'href', $filters)
            ->applyEqFilter($qb, 'author', $filters)
            ->applyValidatedFilter($qb, $filters);

        return $paginatorHelper->create($qb, ['created' => 'DESC', 'validated' => 'DESC']);
    }

    /**
     * @return int
     */
    public function countUnvalidatedItems()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select($qb->expr()->countDistinct('a.id'))
            ->andWhere($qb->expr()->isNull('a.validated'))
            ->andWhere($qb->expr()->isNull('a.invalidated'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
