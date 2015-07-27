<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\Faq;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BloggerFaqController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/faq")
 */
class BloggerFaqController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_faq_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Faq')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_blogger_faq', $faq = new Faq());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($faq);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_faq_read', ['faq' => $faq->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Faq $faq Faq
     *
     * @return array
     * @Route("/{faq}/read", requirements={"faq":"\d+"})
     * @ParamConverter("faq", class="AdcogDefaultBundle:Faq")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Faq $faq)
    {
        return [
            'faq' => $faq,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Faq     $faq     Faq
     *
     * @return array|RedirectResponse
     * @Route("/{faq}/update", requirements={"faq":"\d+"})
     * @ParamConverter("faq", class="AdcogDefaultBundle:Faq")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Faq $faq)
    {
        $form = $this->createForm('adcog_blogger_faq', $faq);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('blogger_faq_read', ['faq' => $faq->getId()]));
        }

        return [
            'faq' => $faq,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Faq     $faq     Faq
     *
     * @return array|RedirectResponse
     * @Route("/{faq}/delete", requirements={"faq":"\d+"})
     * @ParamConverter("faq", class="AdcogDefaultBundle:Faq")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Faq $faq)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($faq);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_faq_index'));
        }

        return [
            'faq' => $faq,
            'form' => $form->createView(),
        ];
    }
}
