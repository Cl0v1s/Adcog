<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\School;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminSchoolController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/school")
 */
class AdminSchoolController extends Controller
{
    /**
     * Index
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_school_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:School')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request Request
     *
     * @return array|RedirectResponse
     * @Route("/create")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('adcog_admin_school', $school = new School());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($school);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_school_read', ['school' => $school->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param School $school School
     *
     * @return array
     * @Route("/{school}/read", requirements={"school":"\d+"})
     * @ParamConverter("school", class="AdcogDefaultBundle:School")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(School $school)
    {
        return [
            'school' => $school,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param School    $school    School
     *
     * @return array|RedirectResponse
     * @Route("/{school}/update", requirements={"school":"\d+"})
     * @ParamConverter("school", class="AdcogDefaultBundle:School")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, School $school)
    {
        $form = $this->createForm('adcog_admin_school', $school);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_school_read', ['school' => $school->getId()]));
        }

        return [
            'school' => $school,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param School    $school    School
     *
     * @return array|RedirectResponse
     * @Route("/{school}/delete", requirements={"school":"\d+"})
     * @ParamConverter("school", class="AdcogDefaultBundle:School")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, School $school)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($school);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_school_index'));
        }

        return [
            'school' => $school,
            'form' => $form->createView(),
        ];
    }
}
