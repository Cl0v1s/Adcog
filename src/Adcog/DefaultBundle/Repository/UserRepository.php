<?php
namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\School;
use Adcog\DefaultBundle\Entity\User;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use EB\TranslationBundle\Translation\TranslationService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserRepository
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * Create a paginator
     *
     * @param PaginatorHelper $paginatorHelper Paginator helper
     * @param array           $filters         Filters
     *
     * @return User[]|Paginator
     */
    public function getPaginator(PaginatorHelper $paginatorHelper, array $filters = [])
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->leftJoin('a.experiences', 'b')
            ->addSelect('b')
            ->leftJoin('a.school', 'c')
            ->addSelect('c')
            ->leftJoin('a.profile', 'd')
            ->addSelect('d');

        // Allow
        if (array_key_exists('allow', $filters) && null !== $allow = $filters['allow']) {
            if (true === $allow) {
                $qb
                    ->innerJoin('a.profile', 'p');
            } else {
                $qb
                    ->andWhere($qb->expr()->isNull('a.profile'));
            }
        }

        $paginatorHelper
            ->applyLikeFilter($qb, 'username', $filters)
            ->applyLikeFilter($qb, 'firstname', $filters)
            ->applyLikeFilter($qb, 'lastname', $filters)
            ->applyEqFilter($qb, 'role', $filters)
            ->applyEqFilter($qb, 'gender', $filters)
            ->applyEqFilter($qb, 'nationality', $filters)
            ->applyEqFilter($qb, 'acceptedContact', $filters)
            ->applyLikeFilter($qb, 'address', $filters)
            ->applyLikeFilter($qb, 'zip', $filters)
            ->applyLikeFilter($qb, 'city', $filters)
            ->applyLikeFilter($qb, 'country', $filters)
            ->applyEqFilter($qb, 'school', $filters)
            ->applyValidatedFilter($qb, $filters);

        // Schools
        if (array_key_exists('schools', $filters) && 0 !== count($schools = $filters['schools'])) {
            if ($schools instanceof ArrayCollection) {
                $qb
                    ->andWhere($qb->expr()->in('c.id', ':schools_ids'))
                    ->setParameter('schools_ids', array_map(function (School $school) {
                        return $school->getId();
                    }, $schools->toArray()));
            }
        }

        return $paginatorHelper->create($qb, ['lastname' => 'ASC', 'firstname' => 'DESC', 'created' => 'DESC']);
    }
    
    /**
    * Export Data (not paginated)
    *
    * @param array           $filters         Filters
    *
    * @return
    */
    public function exportData(array $filters = []) 
    {
        // Recherche les éléments
        $paginatorHelper = new PaginatorHelper();
        $data = $this->getPaginator($paginatorHelper, $filters);
        
        // Retourne les données
        return $data;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = [], $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->leftJoin('a.experiences', 'd')
            ->addSelect('d')
            ->leftJoin('a.school', 'e')
            ->addSelect('e')
            ->leftJoin('a.payments', 'f')
            ->addSelect('f')
            ->leftJoin('a.comments', 'h')
            ->addSelect('h')
            ->leftJoin('a.profile', 'i')
            ->addSelect('i');

        foreach ($criteria as $key => $value) {
            $qb
                ->andWhere($qb->expr()->eq(sprintf('a.%s', $key), sprintf(':%s', $key)))
                ->setParameter($key, $value);
        }

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        return $qb
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param School $school
     *
     * @return User[]
     */
    public function findUsersOfPromotion(School $school)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('a')
            ->innerJoin('a.school', 'b')
            ->andWhere($qb->expr()->eq('b.id', ':id'))
            ->setParameter('id', $school->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * Get school statistics
     *
     * @param bool $fetchEmpty
     *
     * @return array
     */
    public function getSchoolStatistics($fetchEmpty = true)
    {
        $qb = $this->createQueryBuilder('a');

        if (false === $fetchEmpty) {
            $qb
                ->andWhere($qb->expr()->isNotNull('a.school'));
        }

        return $qb
            ->select($qb->expr()->countDistinct('a.id') . ' as user_count')
            ->leftJoin('a.school', 'b')
            ->addSelect('b.name')
            ->addSelect('b.year')
            ->addGroupBy('a.school')
            ->getQuery()
            ->getArrayResult();
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
     * Admins
     *
     * @return User[]
     */
    public function getAdmins()
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('a')
            ->andWhere($qb->expr()->eq('a.role', ':role'))
            ->setParameter('role', User::ROLE_ADMIN)
            ->getQuery()
            ->getResult();
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
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (false === $this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException($user);
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'Adcog\DefaultBundle\Entity\User' === $class;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->createQueryBuilder('a');

        try {
            $user = $qb
                ->leftJoin('a.experiences', 'd')
                ->addSelect('d')
                ->leftJoin('a.school', 'e')
                ->addSelect('e')
                ->leftJoin('a.profile', 'i')
                ->addSelect('i')
                ->andWhere($qb->expr()->eq('a.username', ':username'))
                ->setParameter('username', $username)
                ->getQuery()
                ->getOneOrNullResult();

            if (null !== $user) {
                return $user;
            }
        } catch (\Exception $e) {
        }

        throw new UsernameNotFoundException($username);
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
}
