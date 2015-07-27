<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Employer;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class EmployerRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmployerRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return Employer[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyLikeFilter($qb, 'name', $filters)
            ->applyLikeFilter($qb, 'city', $filters)
            ->applyLikeFilter($qb, 'country', $filters)
            ->applyValidatedFilter($qb, $filters);

        return $paginatorHelper->create($qb, ['name' => 'ASC', 'created' => 'DESC']);
    }

    /**
     * Get city statistics
     *
     * @param bool $fetchEmpty
     *
     * @return array
     */
    public function getCityStatistics($fetchEmpty = true)
    {
        $qb = $this->createQueryBuilder('a');

        if (false === $fetchEmpty) {
            $qb
                ->andWhere($qb->expr()->isNotNull('a.city'));
        }

        return $qb
            ->select($qb->expr()->countDistinct('a.id') . ' as city_count')
            ->addSelect('a.city')
            ->addGroupBy('a.city')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Get country statistics
     *
     * @param bool $fetchEmpty
     *
     * @return array
     */
    public function getCountryStatistics($fetchEmpty = true)
    {
        $qb = $this->createQueryBuilder('a');

        if (false === $fetchEmpty) {
            $qb
                ->andWhere($qb->expr()->isNotNull('a.country'));
        }

        return $qb
            ->select($qb->expr()->countDistinct('a.id') . ' as country_count')
            ->addSelect('a.country')
            ->addGroupBy('a.country')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Autocomplete
     *
     * @param string $query Query
     * @param string $field Field
     *
     * @return array
     */
    public function autocomplete($query, $field = 'zip')
    {
        $qb = $this->createQueryBuilder('a');

        $data = $qb
            ->select(sprintf('DISTINCT a.%s', $field))
            ->andWhere($qb->expr()->like(sprintf('a.%s', $field), ':query'))
            ->setParameter('query', sprintf('%%%s%%', $query))
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->addOrderBy(sprintf('a.%s', $field), 'asc')
            ->getQuery()
            ->getScalarResult();

        return array_map(function (array $item) use ($field) {
            return $item[$field];
        }, $data);
    }

    /**
     * Autocomplete employer by name
     *
     * @param string $query Query
     *
     * @return array
     */
    public function autocompleteEmployer($query)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('a.name AS text,a.id,a.address,a.city,a.country,a.email,a.phone,a.website,a.zip')
            ->andWhere($qb->expr()->like('a.name', ':query'))
            ->setParameter('query', sprintf('%%%s%%', $query))
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->addOrderBy('a.name', 'asc')
            ->getQuery()
            ->getScalarResult();
    }
}