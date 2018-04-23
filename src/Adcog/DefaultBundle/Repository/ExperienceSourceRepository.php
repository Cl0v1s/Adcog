<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\ExperienceSource;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class ExperienceSourceRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceSourceRepository extends EntityRepository
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
            ->applyLikeFilter($qb, 'content', $filters)
            ->applyValidatedFilter($qb, $filters);

        return $paginatorHelper->create($qb);
    }

    /**
     * Find one or create one
     *
     * @param string $content
     *
     * @return ExperienceSource
     */
    public function findOrCreate($content)
    {
        if (null === $experienceSource = $this->findOneBy(['content' => $content])) {
            $experienceSource = new ExperienceSource();
            $experienceSource->setContent($content);
            $this->getEntityManager()->persist($experienceSource);
        }

        return $experienceSource;
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
            ->select('a.content as id,a.content as text')
            ->andWhere($qb->expr()->like('a.content', ':content'))
            ->setParameter('content', sprintf('%%%s%%', $query))
            ->getQuery()
            ->getArrayResult();
    }
}
