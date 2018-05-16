<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class AdminUserController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/user")
 */
class AdminUserController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_user_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
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
     */
    public function exportAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_user_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->exportData($filterData);
        
        // Set Response
        $response = $this->render('AdcogAdminBundle:AdminUser:export.csv.twig',array('data' => $paginator, 'excel_pack' => pack("CCC",0xef,0xbb,0xbf)));
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="exportUsersAdcog.csv"');

        return $response;
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
        $form = $this->createForm('adcog_admin_user', $user = new User());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user_read', [
                'user' => $user->getId(),
            ]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param User $user User
     *
     * @return array
     * @Route("/{user}/read", requirements={"user":"\d+"})
     * @ParamConverter("user", class="AdcogDefaultBundle:User")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(User $user)
    {
        return [
            'user' => $user,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return array|RedirectResponse
     * @Route("/{user}/update", requirements={"user":"\d+"})
     * @ParamConverter("user", class="AdcogDefaultBundle:User")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, User $user)
    {
        $form = $this->createForm('adcog_admin_user', $user);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_user_read', [
                'user' => $user->getId(),
            ]));
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return array|RedirectResponse
     * @Route("/{user}/delete", requirements={"user":"\d+"})
     * @ParamConverter("user", class="AdcogDefaultBundle:User")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($user);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user_index'));
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }
}
