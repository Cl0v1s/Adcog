<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Event;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class EventRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EventRepository extends EntityRepository
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
            ->applyLikeFilter($qb, 'name', $filter)
            ->applyLikeFilter($qb, 'description', $filter);

        return $paginatorHelper->create($qb);
    }

    /**
     * @return Event[]
     */
    public function findIncomingEvents()
    {
        $now = new \DateTime();
        $now->sub(new \DateInterval('P1D'));

        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere($qb->expr()->gte('a.date', ':date'))
            ->setParameter('date', $now)
            ->addOrderBy('a.date', 'asc')
            ->getQuery()
            ->getResult();
    }
}
