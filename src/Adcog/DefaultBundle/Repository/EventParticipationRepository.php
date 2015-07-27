<?php

namespace Adcog\DefaultBundle\Repository;

use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class EventParticipationRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EventParticipationRepository extends EntityRepository
{
    /**
     * Paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filter          Filter
     *
     * @return Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filter = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyEqFilter($qb, 'user', $filter)
            ->applyEqFilter($qb, 'event', $filter)
            ->applyEqFilter($qb, 'type', $filter);

        return $paginatorHelper->create($qb);
    }
}
