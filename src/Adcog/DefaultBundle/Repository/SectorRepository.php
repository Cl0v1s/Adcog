<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Sector;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class SectorRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SectorRepository extends EntityRepository
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
            ->applyLikeFilter($qb, 'name', $filters)
            ->applyValidatedFilter($qb, $filters);

        return $paginatorHelper->create($qb);
    }

    /**
     * Find one or create one
     *
     * @param string $name
     *
     * @return Sector
     */
    public function findOrCreate($name)
    {
        if (null === $sector = $this->findOneBy(['name' => $name])) {
            $sector = new Sector();
            $sector->setName($name);
            $this->getEntityManager()->persist($sector);
        }

        return $sector;
    }

    /**
     * Search
     *
     * @param string $query
     *
     * @return array
     */
    public function search($query)
    {

        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('a.name as id,a.name as text')
            ->andWhere($qb->expr()->like('a.name', ':name'))
            ->setParameter('name', sprintf('%%%s%%', $query))
            ->getQuery()
            ->getArrayResult();
    }
}
