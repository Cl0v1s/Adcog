<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\ContractType;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class ContractTypeRepository
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 */
class ContractTypeRepository extends EntityRepository
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
     * @return ContractType
     */
    public function findOrCreate($content)
    {
        if (null === $contractType = $this->findOneBy(['content' => $content])) {
            $contractType = new ContractType();
            $contractType->setContent($content);
            $this->getEntityManager()->persist($contractType);
        }

        return $contractType;
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
