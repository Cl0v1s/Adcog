<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\Sector;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class ExperienceRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return Experience[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->leftJoin('a.sectors', 'b')
            ->addSelect('b')
            ->innerJoin('a.employer', 'c')
            ->addSelect('c')
            ->innerJoin('a.user', 'd')
            ->addSelect('d')
            ->leftJoin('d.profile', 'e')
            ->addSelect('e');

        // Type
        if (array_key_exists('type', $filters) && (null !== $type = $filters['type'])) {
            $qb
                ->andWhere(sprintf('a INSTANCE OF %s%s', 'Adcog\DefaultBundle\Entity\Experience', mb_convert_case($type, MB_CASE_TITLE)));
        }

        $paginatorHelper
            ->applyLikeFilter($qb, 'description', $filters)
            ->applyEqFilter($qb, 'user', $filters)
            ->applyEqFilter($qb, 'salary', $filters)
            ->applyEqFilter($qb, 'isPublic', $filters)
            ->applyValidatedFilter($qb, $filters, 'd');

        // Sectors
        if (array_key_exists('sectors', $filters) && 0 !== count($sectors = $filters['sectors'])) {
            if ($sectors instanceof ArrayCollection) {
                $qb
                    ->andWhere($qb->expr()->in('b.id', ':sectors_ids'))
                    ->setParameter('sectors_ids', array_map(function (Sector $sector) {
                        return $sector->getId();
                    }, $sectors->toArray()));
            }
        }

        return $paginatorHelper->create($qb, ['started' => 'DESC', 'ended' => 'DESC', 'created' => 'DESC']);
    }

    /**
     * Find all experience with sector
     *
     * @param Sector $sector
     *
     * @return Experience[]
     */
    public function findAllExperienceWithSector(Sector $sector)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->innerJoin('a.sectors', 's')
            ->andWhere($qb->expr()->eq('s', ':sector'))
            ->setParameter('sector', $sector)
            ->getQuery()
            ->getResult();
    }

    /**
     * First experiences
     *
     * @return Experience[]
     */
    public function getFirstExperiences()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere($qb->expr()->isNotNull('a.salary'))
            ->innerJoin('a.user', 'b')
            ->addOrderBy('a.started', 'desc')
            ->addGroupBy('a.user')
            ->getQuery()
            ->getResult();
    }

    /**
     * Last experiences
     *
     * @return Experience[]
     */
    public function getLastExperiences()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere($qb->expr()->isNotNull('a.salary'))
            ->innerJoin('a.user', 'b')
            ->addOrderBy('a.started', 'asc')
            ->addGroupBy('a.user')
            ->getQuery()
            ->getResult();
    }
}
