<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\ExperienceSource;
use Adcog\DefaultBundle\Repository\ExperienceSourceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminExperienceSource
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 * @Route("/experience-source")
 */
class AdminExperienceSourceController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_experience_source_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:ExperienceSource')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_experience_source', $expsource = new ExperienceSource());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($expsource);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_experience_source_read', ['expsource' => $expsource->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param ExperienceSource $expsource ExperienceSource
     *
     * @return array
     * @Route("/{expsource}/read", requirements={"expsource":"\d+"})
     * @ParamConverter("expsource", class="AdcogDefaultBundle:ExperienceSource")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(ExperienceSource $expsource)
    {
        return [
            'expsource' => $expsource,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param ExperienceSource  $expsource  ExperienceSource
     *
     * @return array|RedirectResponse
     * @Route("/{expsource}/update", requirements={"expsource":"\d+"})
     * @ParamConverter("expsource", class="AdcogDefaultBundle:ExperienceSource")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, ExperienceSource $expsource)
    {
        $form = $this->createForm('adcog_admin_experience_source', $expsource);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_experience_source_read', ['expsource' => $expsource->getId()]));
        }

        return [
            'expsource' => $expsource,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param ExperienceSource  $expsource  ExperienceSource
     *
     * @return array|RedirectResponse
     * @Route("/{expsource}/delete", requirements={"expsource":"\d+"})
     * @ParamConverter("expsource", class="AdcogDefaultBundle:ExperienceSource")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, ExperienceSource $expsource)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($expsource);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_experience_source_index'));
        }

        return [
            'expsource' => $expsource,
            'form' => $form->createView(),
        ];
    }

    /**
     * Replace
     *
     * @param Request $request Request
     * @param ExperienceSource  $expsource  ExperienceSource
     *
     * @return array|RedirectResponse
     * @Route("/{expsource}/replace", requirements={"expsource":"\d+"}, name="admin_experience_source_replace")
     * @ParamConverter("expsource", class="AdcogDefaultBundle:ExperienceSource")
     * @Method("GET|POST")
     * @Template()
     */
    public function replaceAction(Request $request, ExperienceSource $expsource)
    {
        $query = function (ExperienceSourceRepository $er) use ($expsource) {
            $qb = $er->createQueryBuilder('a');

            return $qb
                ->andWhere($qb->expr()->neq('a', ':expsource'))
                ->setParameter('expsource', $expsource);
        };

        $form = $this
            ->createFormBuilder()
            ->add('expsource', 'entity', [
                'label' => 'Nouvelle source',
                'class' => 'Adcog\DefaultBundle\Entity\ExperienceSource',
                'query_builder' => $query,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $new = $form->get('expsource')->getData();
            $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator(PaginatorHelper::createEmptyInstance(), [
                'expsources' => [$new],
            ]);
            array_map(function (Experience $experience) use ($expsource, $new) {
                $experience
                    ->removeExperienceSource($expsource)
                    ->addExperienceSource($new);
            }, iterator_to_array($experiences->getIterator()));
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            $em->remove($expsource);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_experience_source_index'));
        }

        return [
            'expsource' => $expsource,
            'form' => $form->createView(),
        ];
    }
}
