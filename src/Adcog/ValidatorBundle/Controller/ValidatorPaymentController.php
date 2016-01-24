<?php

namespace Adcog\ValidatorBundle\Controller;

use Adcog\DefaultBundle\Entity\Payment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ValidatorPaymentController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/payment")
 */
class ValidatorPaymentController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_validator_payment_filter', ['not_validated' => true], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Payment')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
            'filter_attr' => ['not_validated' => true]
        ];
    }
    
    /**
     * Export
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/export")
     * @Method("GET")
     * @Template()
     */
    public function exportAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_validator_payment_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Payment')->exportData($filterData);
        
        // Set Response
        $response = $this->render('AdcogValidatorBundle:ValidatorPayment:export.csv.twig',array('data' => $paginator, 'excel_pack' => pack("CCC",0xef,0xbb,0xbf)));
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }

    /**
     * Read
     *
     * @param Request $request Request
     * @param Payment $payment Payment
     *
     * @return array
     * @Route("/{payment}/read", requirements={"payment":"\d+"})
     * @ParamConverter("payment", class="AdcogDefaultBundle:Payment")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Request $request, Payment $payment)
    {
        $form = $this->createForm('adcog_validator_payment', $payment);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('validator_payment_index'));
        }

        return [
            'payment' => $payment,
            'prices' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Price')->findBy([], ['amount' => 'ASC']),
            'form' => $form->createView(),
        ];
    }
}
