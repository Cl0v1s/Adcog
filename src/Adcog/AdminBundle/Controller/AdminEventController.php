<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminEventController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/event")
 */
class AdminEventController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_event_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Event')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_event', $event = new Event());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_read', ['event' => $event->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Event $event Event
     *
     * @return array
     * @Route("/{event}/read", requirements={"event":"\d+"})
     * @ParamConverter("event", class="AdcogDefaultBundle:Event")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Event $event)
    {
        return [
            'event' => $event,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Event    $event    Event
     *
     * @return array|RedirectResponse
     * @Route("/{event}/update", requirements={"event":"\d+"})
     * @ParamConverter("event", class="AdcogDefaultBundle:Event")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Event $event)
    {
        $form = $this->createForm('adcog_admin_event', $event);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_event_read', ['event' => $event->getId()]));
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Event    $event    Event
     *
     * @return array|RedirectResponse
     * @Route("/{event}/delete", requirements={"event":"\d+"})
     * @ParamConverter("event", class="AdcogDefaultBundle:Event")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Event $event)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($event);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_index'));
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }
}
