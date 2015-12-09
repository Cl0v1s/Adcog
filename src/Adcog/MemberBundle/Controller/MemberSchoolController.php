<?php

namespace Adcog\MemberBundle\Controller;

use Adcog\DefaultBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MemberSchoolController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/promotion")
 */
class MemberSchoolController extends Controller
{
    /**
     * Index
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $paginator = null;
        if (null !== $user = $this->getUser()) {
            if (null !== $school = $user->getSchool()) {
                $paginatorHelper = $this->get('eb_paginator_helper');
                $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getPaginator($paginatorHelper, ['school' => $school]);
            }
        }

        return [
            'paginator' => $paginator,
        ];
    }
}
