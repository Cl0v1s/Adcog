<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Office;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminOfficeController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/office")
 */
class AdminOfficeController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_office_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Office')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_office', $office = new Office());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($office);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_office_read', ['office' => $office->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Office $office Office
     *
     * @return array
     * @Route("/{office}/read", requirements={"office":"\d+"})
     * @ParamConverter("office", class="AdcogDefaultBundle:Office")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Office $office)
    {
        return [
            'office' => $office,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Office    $office    Office
     *
     * @return array|RedirectResponse
     * @Route("/{office}/update", requirements={"office":"\d+"})
     * @ParamConverter("office", class="AdcogDefaultBundle:Office")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Office $office)
    {
        $form = $this->createForm('adcog_admin_office', $office);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_office_read', ['office' => $office->getId()]));
        }

        return [
            'office' => $office,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Office    $office    Office
     *
     * @return array|RedirectResponse
     * @Route("/{office}/delete", requirements={"office":"\d+"})
     * @ParamConverter("office", class="AdcogDefaultBundle:Office")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Office $office)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($office);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_office_index'));
        }

        return [
            'office' => $office,
            'form' => $form->createView(),
        ];
    }
}
