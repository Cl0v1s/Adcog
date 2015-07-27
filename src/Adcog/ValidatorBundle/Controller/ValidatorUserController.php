<?php

namespace Adcog\ValidatorBundle\Controller;

use Adcog\DefaultBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ValidatorUserController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/user")
 */
class ValidatorUserController extends Controller
{
    /**
     * Index
     *
     * @param Request $request
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_validator_user_filter', ['not_validated' => true], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return array
     * @Route("/{user}/read", requirements={"user":"\d+"})
     * @ParamConverter("user", class="AdcogDefaultBundle:User")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Request $request, User $user)
    {
        $form = $this->createForm('adcog_validator_user', $user);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('validator_user_index'));
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }
}
