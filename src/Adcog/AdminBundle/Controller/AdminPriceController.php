<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Price;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminPriceController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/price")
 */
class AdminPriceController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_price_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Price')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_price', $price = new Price());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($price);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_price_read', ['price' => $price->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Price $price Price
     *
     * @return array
     * @Route("/{price}/read", requirements={"price":"\d+"})
     * @ParamConverter("price", class="AdcogDefaultBundle:Price")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Price $price)
    {
        return [
            'price' => $price,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Price    $price    Price
     *
     * @return array|RedirectResponse
     * @Route("/{price}/update", requirements={"price":"\d+"})
     * @ParamConverter("price", class="AdcogDefaultBundle:Price")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Price $price)
    {
        $form = $this->createForm('adcog_admin_price', $price);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_price_read', ['price' => $price->getId()]));
        }

        return [
            'price' => $price,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Price    $price    Price
     *
     * @return array|RedirectResponse
     * @Route("/{price}/delete", requirements={"price":"\d+"})
     * @ParamConverter("price", class="AdcogDefaultBundle:Price")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Price $price)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($price);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_price_index'));
        }

        return [
            'price' => $price,
            'form' => $form->createView(),
        ];
    }
}
