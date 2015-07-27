<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Employer;
use Adcog\DefaultBundle\Repository\EmployerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminEmployerController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/employer")
 */
class AdminEmployerController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_employer_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_employer', $employer = new Employer());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($employer);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_employer_read', ['employer' => $employer->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Employer $employer Employer
     *
     * @return array
     * @Route("/{employer}/read", requirements={"employer":"\d+"})
     * @ParamConverter("employer", class="AdcogDefaultBundle:Employer")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Employer $employer)
    {
        return [
            'employer' => $employer,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Employer    $employer    Employer
     *
     * @return array|RedirectResponse
     * @Route("/{employer}/update", requirements={"employer":"\d+"})
     * @ParamConverter("employer", class="AdcogDefaultBundle:Employer")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Employer $employer)
    {
        $form = $this->createForm('adcog_admin_employer', $employer);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_employer_read', ['employer' => $employer->getId()]));
        }

        return [
            'employer' => $employer,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Employer    $employer    Employer
     *
     * @return array|RedirectResponse
     * @Route("/{employer}/delete", requirements={"employer":"\d+"})
     * @ParamConverter("employer", class="AdcogDefaultBundle:Employer")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Employer $employer)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($employer);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_employer_index'));
        }

        return [
            'employer' => $employer,
            'form' => $form->createView(),
        ];
    }

    /**
     * Replace
     *
     * @param Request  $request  Request
     * @param Employer $employer Employer
     *
     * @return array|RedirectResponse
     * @Route("/{employer}/replace", requirements={"employer":"\d+"}, name="admin_employer_replace")
     * @ParamConverter("employer", class="AdcogDefaultBundle:Employer")
     * @Method("GET|POST")
     * @Template()
     */
    public function replaceAction(Request $request, Employer $employer)
    {
        $query = function (EmployerRepository $er) use ($employer) {
            $qb = $er->createQueryBuilder('a');

            return $qb
                ->andWhere($qb->expr()->neq('a', ':employer'))
                ->setParameter('employer', $employer);
        };

        $form = $this
            ->createFormBuilder()
            ->add('employer', 'entity', [
                'label' => 'Nouvel établissement',
                'class' => 'Adcog\DefaultBundle\Entity\Employer',
                'query_builder' => $query,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $new = $form->get('employer')->getData();
            $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->findBy([
                'employer' => $employer,
            ]);
            foreach ($experiences as $experience) {
                $experience->setEmployer($new);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            $em->remove($employer);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_employer_index'));
        }

        return [
            'employer' => $employer,
            'form' => $form->createView(),
        ];
    }
}
