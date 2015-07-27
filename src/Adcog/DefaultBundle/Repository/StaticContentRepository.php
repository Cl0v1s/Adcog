<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\StaticContent;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class StaticContentRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class StaticContentRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return StaticContent[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyEqFilter($qb, 'type', $filters)
            ->applyLikeFilter($qb, 'title', $filters)
            ->applyLikeFilter($qb, 'content', $filters);

        return $paginatorHelper->create($qb, ['title' => 'ASC']);
    }
}
