<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\School;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class SchoolRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SchoolRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return School[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyEqFilter($qb, 'year', $filters)
            ->applyLikeFilter($qb, 'name', $filters);

        return $paginatorHelper->create($qb, ['year' => 'DESC']);
    }
}
