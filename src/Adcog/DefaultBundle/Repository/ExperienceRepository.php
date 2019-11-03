<?php

namespace Adcog\DefaultBundle\Repository;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\ExperienceStudy;
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
            ->applyLikeFilter($qb, 'country', $filters, 'c')
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

        // Place
        if (array_key_exists('place', $filters) && strlen($filters['place']) > 0) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('c.address', ':place'),
                $qb->expr()->like('c.zip', ':place'),
                $qb->expr()->like('c.city', ':place')
            ))
            ->setParameter('place', '%'.$filters['place'].'%');
        }

        // Name
        if (array_key_exists('name', $filters) && strlen($filters['name']) > 0) {
            $tqb = $this->createQueryBuilder('z') 
                ->from('Adcog\DefaultBundle\Entity\ExperienceWork', 'w')
                ->addSelect('w.id as id');
            $tqb->where($tqb->expr()->like('w.workPosition', ':name'));
            $tqb->setParameter('name', '%'.$filters['name'].'%');
            $q = $tqb->getQuery(); 
            $results = $q->getArrayResult();
            $works = array_map(function($a){ return $a['id']; }, $results);

            $tqb = $this->createQueryBuilder('z') 
                ->from('Adcog\DefaultBundle\Entity\ExperienceInternship', 'w')
                ->addSelect('w.id as id');
            $tqb->where(
                $tqb->expr()->orX(
                    $tqb->expr()->like('w.internshipSubject', ':name'),
                    $tqb->expr()->like('w.tuteur', ':name')
                )
            );
            $tqb->setParameter('name', '%'.$filters['name'].'%');
            $q = $tqb->getQuery(); 
            $results = $q->getArrayResult();
            $interns = array_map(function($a){ return $a['id']; }, $results);

            $tqb = $this->createQueryBuilder('z') 
                ->from('Adcog\DefaultBundle\Entity\ExperienceStudy', 'w')
                ->addSelect('w.id as id');
            $tqb->where($tqb->expr()->like('w.studyDiploma', ':name'));
            $tqb->setParameter('name', '%'.$filters['name'].'%');
            $q = $tqb->getQuery(); 
            $results = $q->getArrayResult();
            $studies = array_map(function($a){ return $a['id']; }, $results);

            $tqb = $this->createQueryBuilder('z') 
            ->from('Adcog\DefaultBundle\Entity\ExperienceThesis', 'w')
            ->addSelect('w.id as id');
            $tqb->where(
                $tqb->expr()->orX(
                    $tqb->expr()->like('w.thesisDiscipline', ':name'),
                    $tqb->expr()->like('w.thesisSubject', ':name'),
                    $tqb->expr()->like('w.thesisType', ':name')
                )
            );
            $tqb->setParameter('name', '%'.$filters['name'].'%');
            $q = $tqb->getQuery(); 
            $results = $q->getArrayResult();
            $thesis = array_map(function($a){ return $a['id']; }, $results);


            $conditions = [];
            array_push($conditions, $qb->expr()->like('c.name', ':name'));
            array_push($conditions, $qb->expr()->like('a.description', ':name'));
            if(count($works) > 0) {
                array_push($conditions, $qb->expr()->in('a.id', $works));
            }
            if(count($studies) > 0) {
                array_push($conditions, $qb->expr()->in('a.id', $studies));
            }
            if(count($thesis) > 0) {
                array_push($conditions, $qb->expr()->in('a.id', $thesis));
            }
            if(count($interns) > 0) {
                array_push($conditions, $qb->expr()->in('a.id', $interns));
            }

            $qb->andWhere($qb->expr()->orX(
                ...$conditions
            ));
            $qb->setParameter('name', '%'.$filters['name'].'%');
        }



        $res = $paginatorHelper->create($qb, ['started' => 'DESC', 'ended' => 'DESC', 'created' => 'DESC']);

        return $res;
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

    /**
     * find By sector
     *
     * @return Experience[]
     */

    public function findBySector($sector)
    {
        $query = $this->createQueryBuilder('e')
                      ->select('e')
                      ->leftJoin('e.sector', 's')
                      ->addSelect('s');
 
        $query = $query->add('where', $query->expr()->in('s', ':s'))
                      ->setParameter('s', $sector)
                      ->getQuery()
                      ->getResult();
          
        return $query;
    }

    /**
     * count nbr studies
     * @return int
     */

    public function getStudiesNumber($id) {
        $qb = $this->createQueryBuilder('e');

        return $qb
            ->select('COUNT(e)')
            ->innerJoin('e.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->andWhere('e INSTANCE OF :expStudy')
            ->setParameter('expStudy', 'study')
            ->getQuery()
            ->getSingleScalarResult();
    }

     /**
     * count nbr experiences != sudies
     * @return int
     */

    public function getExperiencesNumber($id) {
        $qb = $this->createQueryBuilder('e');

        return $qb
            ->select('COUNT(e)')
            ->innerJoin('e.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->andWhere('e NOT INSTANCE OF :expStudy')
            ->setParameter('expStudy', 'study')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
