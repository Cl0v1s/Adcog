<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\FaqCategory;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class FaqCategoryRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class FaqCategoryRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return FaqCategory[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyLikeFilter($qb, 'name', $filters);

        return $paginatorHelper->create($qb, ['name' => 'ASC', 'created' => 'DESC']);
    }

    /**
     * Faqs by category
     *
     * @return FaqCategory[]
     */
    public function findFaqsByCategory()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->leftJoin('a.faqs', 'f')
            ->addSelect('f')
            ->addOrderBy('a.name', 'ASC')
            ->addOrderBy('f.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
