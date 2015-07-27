<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Tag;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class TagRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TagRepository extends EntityRepository
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

        $paginatorHelper->applyLikeFilter($qb, 'content', $filters);

        return $paginatorHelper->create($qb);
    }

    /**
     * Find one or create one
     *
     * @param string $content
     *
     * @return Tag
     */
    public function findOrCreate($content)
    {
        if (null === $tag = $this->findOneBy(['content' => $content])) {
            $tag = new Tag();
            $tag->setContent($content);
            $this->getEntityManager()->persist($tag);
        }

        return $tag;
    }

    /**
     * Search
     *
     * @param string $query
     *
     * @return array
     */
    public function autocomplete($query)
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
