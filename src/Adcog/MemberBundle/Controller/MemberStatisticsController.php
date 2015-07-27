<?php

namespace Adcog\MemberBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MemberStatisticsController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/statistiques")
 */
class MemberStatisticsController extends Controller
{
    /**
     * Index
     *
     * @return RedirectResponse
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        $userCountQb = $em->getRepository('AdcogDefaultBundle:User')->createQueryBuilder('a');
        $userCountQb->select($userCountQb->expr()->countDistinct('a.id'));
        $userCount = (int) $userCountQb->getQuery()->getSingleScalarResult();

        $experienceCountQb = $em->getRepository('AdcogDefaultBundle:Experience')->createQueryBuilder('a');
        $experienceCountQb->select($experienceCountQb->expr()->countDistinct('a.id'));
        $experienceCount = (int) $experienceCountQb->getQuery()->getSingleScalarResult();

        $employerCountQb = $em->getRepository('AdcogDefaultBundle:Employer')->createQueryBuilder('a');
        $employerCountQb->select($employerCountQb->expr()->countDistinct('a.id'));
        $employerCount = (int) $employerCountQb->getQuery()->getSingleScalarResult();

        $userWithExperiencesQb = $em->getRepository('AdcogDefaultBundle:User')->createQueryBuilder('a');
        $userWithExperiencesQb->select($userWithExperiencesQb->expr()->countDistinct('a.id'));
        $userWithExperiencesQb->innerJoin('a.experiences', 'e');
        $userWithExperiences = (int) $userWithExperiencesQb->getQuery()->getSingleScalarResult();

        return [
            'userCount' => $userCount,
            'experienceCount' => $experienceCount,
            'employerCount' => $employerCount,
            'userWithExperiences' => $userWithExperiences,
        ];
    }
}
