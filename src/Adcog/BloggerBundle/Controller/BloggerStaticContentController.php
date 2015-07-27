<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\StaticContent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BloggerStaticContentController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/static-content")
 */
class BloggerStaticContentController extends Controller
{
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
        $form = $this->createForm('adcog_blogger_static_content', $staticContent = new StaticContent());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($staticContent);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_static_content_read', ['staticContent' => $staticContent->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request       $request       Request
     * @param StaticContent $staticContent StaticContent
     *
     * @return array|RedirectResponse
     * @Route("/{staticContent}/delete", requirements={"staticContent":"\d+"})
     * @ParamConverter("staticContent", class="AdcogDefaultBundle:StaticContent")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, StaticContent $staticContent)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($staticContent);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_static_content_index'));
        }

        return [
            'staticContent' => $staticContent,
            'form' => $form->createView(),
        ];
    }

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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_static_content_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:StaticContent')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Read
     *
     * @param StaticContent $staticContent StaticContent
     *
     * @return array
     * @Route("/{staticContent}/read", requirements={"staticContent":"\d+"})
     * @ParamConverter("staticContent", class="AdcogDefaultBundle:StaticContent")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(StaticContent $staticContent)
    {
        return [
            'staticContent' => $staticContent,
        ];
    }

    /**
     * Update
     *
     * @param Request       $request       Request
     * @param StaticContent $staticContent StaticContent
     *
     * @return array|RedirectResponse
     * @Route("/{staticContent}/update", requirements={"staticContent":"\d+"})
     * @ParamConverter("staticContent", class="AdcogDefaultBundle:StaticContent")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, StaticContent $staticContent)
    {
        $form = $this->createForm('adcog_blogger_static_content', $staticContent);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('blogger_static_content_read', ['staticContent' => $staticContent->getId()]));
        }

        return [
            'staticContent' => $staticContent,
            'form' => $form->createView(),
        ];
    }
}
