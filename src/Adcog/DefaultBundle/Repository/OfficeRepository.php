<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Office;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class OfficeRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class OfficeRepository extends EntityRepository
{
    /**
     * Paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyEqFilter($qb, 'user', $filters)
            ->applyEqFilter($qb, 'role', $filters);

        // Active
        if (array_key_exists('active', $filters) && null !== $active = $filters['active']) {
            $now = new \DateTime();
            if (true === $active) {
                $qb
                    ->andWhere($qb->expr()->lte('a.started', ':now'))
                    ->andWhere($qb->expr()->orX(
                        $qb->expr()->isNull('a.ended'),
                        $qb->expr()->gte('a.ended', ':now')
                    ))
                    ->setParameter('now', $now);
            } else {
                $qb
                    ->andWhere($qb->expr()->orX(
                        $qb->expr()->gte('a.started', ':now'),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('a.ended'),
                            $qb->expr()->lte('a.ended', ':now')
                        )
                    ))
                    ->setParameter('now', $now);
            }
        }

        return $paginatorHelper->create($qb);
    }

    /**
     * Get current staff
     *
     * @return Office[]
     */
    public function getCurrent()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->innerJoin('a.role', 'r')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->lte('a.started', ':now'),
                    $qb->expr()->isNull('a.ended')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('a.started', ':now'),
                    $qb->expr()->gte('a.ended', ':now')
                )
            ))
            ->setParameter('now', new \DateTime())
            ->addOrderBy('r.order', 'desc')
            ->addOrderBy('r.id', 'asc')
            ->getQuery()
            ->getResult();
    }
}
