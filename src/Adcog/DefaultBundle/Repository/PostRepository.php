<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Post;
use Adcog\DefaultBundle\Entity\Tag;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class PostRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PostRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return Post[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t');

        // Tagged
        if (array_key_exists('tag', $filters) && (null !== $tag = $filters['tag'])) {
            $qb
                ->innerJoin('a.tags', 't2')
                ->andWhere($qb->expr()->eq('t2', ':tag'))
                ->setParameter('tag', $tag);
        }

        $paginatorHelper
            ->applyLikeFilter($qb, 'title', $filters)
            ->applyLikeFilter($qb, 'text', $filters)
            ->applyEqFilter($qb, 'author', $filters)
            ->applyValidatedFilter($qb, $filters);

        return $paginatorHelper->create($qb, ['created' => 'DESC']);
    }

    /**
     * Find recent posts
     *
     * @param int $limit
     *
     * @return Post[]
     */
    public function findRecent($limit = 5)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere($qb->expr()->isNotNull('a.validated'))
            ->orderBy('a.created', 'desc')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all post tagued with
     *
     * @param Tag $tag
     *
     * @return Post[]
     */
    public function findAllPostTaguedWith(Tag $tag)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->innerJoin('a.tags', 't')
            ->addSelect('t')
            ->innerJoin('a.tags', 't2')
            ->andWhere($qb->expr()->eq('t2', ':tag'))
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();
    }
}
