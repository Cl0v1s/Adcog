<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Comment;
use Adcog\DefaultBundle\Entity\Post;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class CommentRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class CommentRepository extends EntityRepository
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return Comment[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $paginatorHelper
            ->applyEqFilter($qb, 'author', $filters)
            ->applyEqFilter($qb, 'post', $filters)
            ->applyLikeFilter($qb, 'text', $filters)
            ->applyValidatedFilter($qb, $filters);

        return $paginatorHelper->create($qb, ['created' => 'DESC']);
    }

    /**
     * @return int
     */
    public function countUnvalidatedItems()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select($qb->expr()->countDistinct('a.id'))
            ->andWhere($qb->expr()->isNull('a.validated'))
            ->andWhere($qb->expr()->isNull('a.invalidated'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find for post
     *
     * @param Post $post
     *
     * @return Comment[]
     */
    public function findForPost(Post $post)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->andWhere($qb->expr()->eq('a.post', ':post'))
            ->setParameter('post', $post)
            ->andWhere($qb->expr()->isNotNull('a.validated'))
            ->addOrderBy('a.created', 'asc')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find popular comments
     *
     * @param int $limit
     *
     * @return Comment[]
     */
    public function findPopular($limit = 10)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->addSelect('p')
            ->innerJoin('a.post', 'p')
            ->andWhere($qb->expr()->isNotNull('p.validated'))
            ->andWhere($qb->expr()->isNotNull('a.validated'))
            ->orderBy('a.created', 'desc')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
