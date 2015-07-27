<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Payment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AdminPaymentController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/payment")
 */
class AdminPaymentController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_payment_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Payment')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request     $request Request
     * @param null|string $type    Type
     * @param null|int    $price   Price
     *
     * @return array|RedirectResponse
     * @throws HttpException
     * @Route("/create/{type}/{price}", requirements={"price":"\d+"})
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request, $type = null, $price = null)
    {
        if (null === $type || false === in_array($type, Payment::getTypeList())) {
            $whichBuilder = $this->get('form.factory')->createNamedBuilder(null, 'form', [], ['method' => 'GET', 'csrf_protection' => false]);
            $whichBuilder->add('type', 'choice', ['label' => 'Type', 'choices' => Payment::getTypeNameList()]);
            $form = $whichBuilder->getForm();
            if ($form->handleRequest($request)->isValid()) {
                return $this->redirect($this->generateUrl('admin_payment_create', ['type' => $form->get('type')->getData()]));
            }
        } elseif (null === $price || null === $price = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Price')->find($price)) {
            $whichBuilder = $this->get('form.factory')->createNamedBuilder(null, 'form', [], ['method' => 'GET', 'csrf_protection' => false]);
            $whichBuilder->add('price', 'entity', ['label' => 'Tarif', 'class' => 'Adcog\DefaultBundle\Entity\Price']);
            $form = $whichBuilder->getForm();
            if ($form->handleRequest($request)->isValid()) {
                return $this->redirect($this->generateUrl('admin_payment_create', ['type' => $type, 'price' => $form->get('price')->getData()->getId()]));
            }
        } elseif(null !== $price && null !== $price = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Price')->find($price)) {
            $payment = Payment::createObject($type, $price);
            $payment->setUser($this->getUser());
            $form = $this->createForm('adcog_admin_payment', $payment);
            if ($form->handleRequest($request)->isValid()) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($payment);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_payment_read', ['payment' => $payment->getId()]));
            }
        } else {
            throw new HttpException(400);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Payment $payment Payment
     *
     * @return array
     * @Route("/{payment}/read", requirements={"payment":"\d+"})
     * @ParamConverter("payment", class="AdcogDefaultBundle:Payment")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Payment $payment)
    {
        return [
            'payment' => $payment,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Payment $payment Payment
     *
     * @return array|RedirectResponse
     * @Route("/{payment}/update", requirements={"payment":"\d+"})
     * @ParamConverter("payment", class="AdcogDefaultBundle:Payment")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Payment $payment)
    {
        $form = $this->createForm(sprintf('adcog_payment_%s', $payment->getType()), $payment);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_payment_read', ['payment' => $payment->getId()]));
        }

        return [
            'payment' => $payment,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Payment $payment Payment
     *
     * @return array|RedirectResponse
     * @Route("/{payment}/delete", requirements={"payment":"\d+"})
     * @ParamConverter("payment", class="AdcogDefaultBundle:Payment")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Payment $payment)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($payment);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_payment_index'));
        }

        return [
            'payment' => $payment,
            'form' => $form->createView(),
        ];
    }
}
