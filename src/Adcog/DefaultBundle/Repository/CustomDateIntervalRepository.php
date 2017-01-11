<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\CustomDateInterval;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class FileRepository
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 */
class CustomDateIntervalRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return CustomDateInterval[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        return $paginatorHelper->create($qb, ['created' => 'DESC']);
    }
}