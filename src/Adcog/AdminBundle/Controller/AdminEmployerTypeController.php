<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\EmployerType;
use Adcog\DefaultBundle\Repository\EmployerTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminEmployerType
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 * @Route("/employer-type")
 */
class AdminEmployerTypeController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_employer_type_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:EmployerType')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_employer_type', $employerType = new EmployerType());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($employerType);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_employer_type_read', ['employerType' => $employerType->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param EmployerType $employerType EmployerType
     *
     * @return array
     * @Route("/{employerType}/read", requirements={"employerType":"\d+"})
     * @ParamConverter("employerType", class="AdcogDefaultBundle:EmployerType")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(EmployerType $employerType)
    {
        return [
            'employerType' => $employerType,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param EmployerType  $employerType  EmployerType
     *
     * @return array|RedirectResponse
     * @Route("/{employerType}/update", requirements={"employerType":"\d+"})
     * @ParamConverter("employerType", class="AdcogDefaultBundle:EmployerType")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, EmployerType $employerType)
    {
        $form = $this->createForm('adcog_admin_employer_type', $employerType);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_employer_type_read', ['employer' => $employerType->getId()]));
        }

        return [
            'employerType' => $employerType,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param EmployerType  $employerType  EmployerType
     *
     * @return array|RedirectResponse
     * @Route("/{employerType}/delete", requirements={"employerType":"\d+"})
     * @ParamConverter("employerType", class="AdcogDefaultBundle:EmployerType")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, EmployerType $employerType)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($employerType);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_employer_type_index'));
        }

        return [
            'employerType' => $employerType,
            'form' => $form->createView(),
        ];
    }

    /**
     * Replace
     *
     * @param Request $request Request
     * @param EmployerType  $employerType  EmployerType
     *
     * @return array|RedirectResponse
     * @Route("/{employerType}/replace", requirements={"employerType":"\d+"}, name="admin_employer_type_replace")
     * @ParamConverter("employerType", class="AdcogDefaultBundle:EmployerType")
     * @Method("GET|POST")
     * @Template()
     */
    public function replaceAction(Request $request, EmployerType $employerType)
    {
        $query = function (EmployerTypeRepository $er) use ($employerType) {
            $qb = $er->createQueryBuilder('a');

            return $qb
                ->andWhere($qb->expr()->neq('a', ':employerType'))
                ->setParameter('employerType', $employerType);
        };

        $form = $this
            ->createFormBuilder()
            ->add('employerType', 'entity', [
                'label' => 'Nouveau type d\'entreprise',
                'class' => 'Adcog\DefaultBundle\Entity\EmployerType',
                'query_builder' => $query,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $new = $form->get('employerType')->getData();
            $employers = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->findBy([
                'employerType' => $employerType,
            ]);
            foreach ($employers as $employer) {
                $employer->setEmployerType($new);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            $em->remove($employerType);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_employer_type_index'));
        }

        return [
            'employerType' => $employerType,
            'form' => $form->createView(),
        ];
    }
}
