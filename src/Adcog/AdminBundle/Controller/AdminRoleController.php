<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminRoleController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/role")
 */
class AdminRoleController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_role_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Role')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_role', $role = new Role());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($role);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_role_read', ['role' => $role->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Role $role Role
     *
     * @return array
     * @Route("/{role}/read", requirements={"role":"\d+"})
     * @ParamConverter("role", class="AdcogDefaultBundle:Role")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Role $role)
    {
        return [
            'role' => $role,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Role    $role    Role
     *
     * @return array|RedirectResponse
     * @Route("/{role}/update", requirements={"role":"\d+"})
     * @ParamConverter("role", class="AdcogDefaultBundle:Role")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Role $role)
    {
        $form = $this->createForm('adcog_admin_role', $role);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_role_read', ['role' => $role->getId()]));
        }

        return [
            'role' => $role,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Role    $role    Role
     *
     * @return array|RedirectResponse
     * @Route("/{role}/delete", requirements={"role":"\d+"})
     * @ParamConverter("role", class="AdcogDefaultBundle:Role")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Role $role)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($role);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_role_index'));
        }

        return [
            'role' => $role,
            'form' => $form->createView(),
        ];
    }
}
