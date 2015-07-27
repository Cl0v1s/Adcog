<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\Sector;
use EB\DoctrineBundle\Paginator\PaginatorHelper;
use Adcog\DefaultBundle\Repository\SectorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminSectorController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/sector")
 */
class AdminSectorController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_sector_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Sector')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_admin_sector', $sector = new Sector());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($sector);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_sector_read', ['sector' => $sector->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Sector $sector Sector
     *
     * @return array
     * @Route("/{sector}/read", requirements={"sector":"\d+"})
     * @ParamConverter("sector", class="AdcogDefaultBundle:Sector")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Sector $sector)
    {
        return [
            'sector' => $sector,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Sector  $sector  Sector
     *
     * @return array|RedirectResponse
     * @Route("/{sector}/update", requirements={"sector":"\d+"})
     * @ParamConverter("sector", class="AdcogDefaultBundle:Sector")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Sector $sector)
    {
        $form = $this->createForm('adcog_admin_sector', $sector);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_sector_read', ['sector' => $sector->getId()]));
        }

        return [
            'sector' => $sector,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Sector  $sector  Sector
     *
     * @return array|RedirectResponse
     * @Route("/{sector}/delete", requirements={"sector":"\d+"})
     * @ParamConverter("sector", class="AdcogDefaultBundle:Sector")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Sector $sector)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($sector);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_sector_index'));
        }

        return [
            'sector' => $sector,
            'form' => $form->createView(),
        ];
    }

    /**
     * Replace
     *
     * @param Request $request Request
     * @param Sector  $sector  Sector
     *
     * @return array|RedirectResponse
     * @Route("/{sector}/replace", requirements={"sector":"\d+"}, name="admin_sector_replace")
     * @ParamConverter("sector", class="AdcogDefaultBundle:Sector")
     * @Method("GET|POST")
     * @Template()
     */
    public function replaceAction(Request $request, Sector $sector)
    {
        $query = function (SectorRepository $er) use ($sector) {
            $qb = $er->createQueryBuilder('a');

            return $qb
                ->andWhere($qb->expr()->neq('a', ':sector'))
                ->setParameter('sector', $sector);
        };

        $form = $this
            ->createFormBuilder()
            ->add('sector', 'entity', [
                'label' => 'Nouvel établissement',
                'class' => 'Adcog\DefaultBundle\Entity\Sector',
                'query_builder' => $query,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $new = $form->get('sector')->getData();
            $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator(PaginatorHelper::createEmptyInstance(), [
                'sectors' => [$new],
            ]);
            array_map(function (Experience $experience) use ($sector, $new) {
                $experience
                    ->removeSector($sector)
                    ->addSector($new);
            }, iterator_to_array($experiences->getIterator()));
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            $em->remove($sector);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_sector_index'));
        }

        return [
            'sector' => $sector,
            'form' => $form->createView(),
        ];
    }
}
