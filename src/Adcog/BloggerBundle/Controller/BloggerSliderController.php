<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\Slider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BloggerSliderController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/slider")
 */
class BloggerSliderController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_slider_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Slider')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_blogger_slider', $slider = new Slider());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($slider);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_slider_read', ['slider' => $slider->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Slider $slider Slider
     *
     * @return array
     * @Route("/{slider}/read", requirements={"slider":"\d+"})
     * @ParamConverter("slider", class="AdcogDefaultBundle:Slider")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Slider $slider)
    {
        return [
            'slider' => $slider,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Slider    $slider    Slider
     *
     * @return array|RedirectResponse
     * @Route("/{slider}/update", requirements={"slider":"\d+"})
     * @ParamConverter("slider", class="AdcogDefaultBundle:Slider")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Slider $slider)
    {
        $form = $this->createForm('adcog_blogger_slider', $slider);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('blogger_slider_read', ['slider' => $slider->getId()]));
        }

        return [
            'slider' => $slider,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Slider    $slider    Slider
     *
     * @return array|RedirectResponse
     * @Route("/{slider}/delete", requirements={"slider":"\d+"})
     * @ParamConverter("slider", class="AdcogDefaultBundle:Slider")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Slider $slider)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($slider);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_slider_index'));
        }

        return [
            'slider' => $slider,
            'form' => $form->createView(),
        ];
    }
}
