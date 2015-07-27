<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\EventParticipation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminEventParticipationController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/event-participation")
 */
class AdminEventParticipationController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_event_participation_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:EventParticipation')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_event_participation', $eventParticipation = new EventParticipation());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($eventParticipation);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_participation_read', ['eventParticipation' => $eventParticipation->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param EventParticipation $eventParticipation EventParticipation
     *
     * @return array
     * @Route("/{eventParticipation}/read", requirements={"eventParticipation":"\d+"})
     * @ParamConverter("eventParticipation", class="AdcogDefaultBundle:EventParticipation")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(EventParticipation $eventParticipation)
    {
        return [
            'eventParticipation' => $eventParticipation,
        ];
    }

    /**
     * Update
     *
     * @param Request            $request            Request
     * @param EventParticipation $eventParticipation EventParticipation
     *
     * @return array|RedirectResponse
     * @Route("/{eventParticipation}/update", requirements={"eventParticipation":"\d+"})
     * @ParamConverter("eventParticipation", class="AdcogDefaultBundle:EventParticipation")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, EventParticipation $eventParticipation)
    {
        $form = $this->createForm('adcog_admin_event_participation', $eventParticipation);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_event_participation_read', ['eventParticipation' => $eventParticipation->getId()]));
        }

        return [
            'eventParticipation' => $eventParticipation,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request            $request            Request
     * @param EventParticipation $eventParticipation EventParticipation
     *
     * @return array|RedirectResponse
     * @Route("/{eventParticipation}/delete", requirements={"eventParticipation":"\d+"})
     * @ParamConverter("eventParticipation", class="AdcogDefaultBundle:EventParticipation")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, EventParticipation $eventParticipation)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($eventParticipation);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_participation_index'));
        }

        return [
            'eventParticipation' => $eventParticipation,
            'form' => $form->createView(),
        ];
    }
}
