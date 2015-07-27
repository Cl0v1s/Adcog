<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\FaqCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BloggerFaqCategoryController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/faq-category")
 */
class BloggerFaqCategoryController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_faq_category_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:FaqCategory')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_blogger_faq_category', $faqCategory = new FaqCategory());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($faqCategory);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_faq_category_read', ['faqCategory' => $faqCategory->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param FaqCategory $faqCategory FaqCategory
     *
     * @return array
     * @Route("/{faqCategory}/read", requirements={"faqCategory":"\d+"})
     * @ParamConverter("faqCategory", class="AdcogDefaultBundle:FaqCategory")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(FaqCategory $faqCategory)
    {
        return [
            'faqCategory' => $faqCategory,
        ];
    }

    /**
     * Update
     *
     * @param Request     $request     Request
     * @param FaqCategory $faqCategory FaqCategory
     *
     * @return array|RedirectResponse
     * @Route("/{faqCategory}/update", requirements={"faqCategory":"\d+"})
     * @ParamConverter("faqCategory", class="AdcogDefaultBundle:FaqCategory")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, FaqCategory $faqCategory)
    {
        $form = $this->createForm('adcog_blogger_faq_category', $faqCategory);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('blogger_faq_category_read', ['faqCategory' => $faqCategory->getId()]));
        }

        return [
            'faqCategory' => $faqCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request     $request     Request
     * @param FaqCategory $faqCategory FaqCategory
     *
     * @return array|RedirectResponse
     * @Route("/{faqCategory}/delete", requirements={"faqCategory":"\d+"})
     * @ParamConverter("faqCategory", class="AdcogDefaultBundle:FaqCategory")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, FaqCategory $faqCategory)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($faqCategory);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_faq_category_index'));
        }

        return [
            'faqCategory' => $faqCategory,
            'form' => $form->createView(),
        ];
    }
}
